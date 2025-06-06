<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

class HeadsSeeder extends Seeder
{
    private $targetSections = [
        'Отделы',
        'Высшие школы и институты'
    ];

    private $allowedUnits = [
        'Высшая школа экономики и управления',
        'Высшая школа электроники и компьютерных наук',
        'Отдел',
        'Кафедра'
    ];

    function mb_ucfirst(string $string): string
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_strtolower(mb_substr($string, 1));
    }

    private function parseEmail(Crawler $row): ?string
    {
        try {
            $emailCell = $row->filter('td.views-field-field-email');
            if ($emailCell->count() === 0) return null;

            // Получаем весь текст ячейки и разбиваем на строки
            $rawText = $emailCell->text();
            $lines = explode("\n", $rawText);

            // Ищем последнюю непустую строку
            $emailLine = null;
            for ($i = count($lines) - 1; $i >= 0; $i--) {
                $line = trim($lines[$i]);
                if (!empty($line)) {
                    $emailLine = $line;
                    break;
                }
            }

            // Обработка формата "E-mail: ..."
            $emailLine = preg_replace('/^E-mail:\s*/i', '', $emailLine);

            // Нормализация
            $emailNormalized = str_ireplace(
                ['[at]', '[dot]', ' '],
                ['@', '.', ''],
                trim($emailLine)
            );

            // Валидация
            if (filter_var($emailNormalized, FILTER_VALIDATE_EMAIL)) {
                return $emailNormalized;
            }

            return null;

        } catch (\Exception $e) {
            $this->command->error("Ошибка парсинга email: " . $e->getMessage());
            return null;
        }
    }

    public function run()
    {
        $browser = new HttpBrowser(HttpClient::create());
        $crawler = $browser->request('GET', 'https://www.susu.ru/ru/university/official/structure');

        $currentSection = null;
        $rows = $crawler->filter('tr');

        foreach ($rows as $row) {
            $rowCrawler = new Crawler($row);

            // Определение раздела
            if ($rowCrawler->filter('td.views-field-title')->count() > 0) {
                $sectionName = trim($rowCrawler->filter('td.views-field-title')->text());
                if (in_array($sectionName, $this->targetSections)) {
                    $currentSection = $sectionName;
                    $this->command->info("=== АКТИВНЫЙ РАЗДЕЛ: {$currentSection} ===");
                    continue;
                }
            }

            // Обрабатываем только целевые разделы
            if (!$currentSection) continue;

            // Парсим только строки с данными
            if ($rowCrawler->filter('td.views-field-title')->count() === 0) continue;

            // Нормализация названия
            $unitName = trim($rowCrawler->filter('td.views-field-title')->text());
            $unitName = str_replace(['«', '»'], '', $unitName);
            $this->command->info("Обработка: {$unitName}");

            // Фильтрация подразделений (с точным совпадением)
            $isAllowed = false;
            foreach ($this->allowedUnits as $pattern) {
                if (str_starts_with($unitName, $pattern)) {
                    $isAllowed = true;
                    break;
                }
            }
            if (!$isAllowed) {
                $this->command->warn("ПРОПУЩЕНО: {$unitName}");
                continue;
            }

            // Парсинг руководителя
            $headFull = $rowCrawler->filter('td.views-field-field-head')->text();
            $parts = explode(',', $headFull, 2);
            $headName = trim($parts[0]);
            $positionName = isset($parts[1])
                ? $this->mb_ucfirst(trim($parts[1]))
                : 'Руководитель';

            // Email (добавлена проверка структуры)
            $email = $this->parseEmail($rowCrawler);
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = Str::slug($headName).'@susu.ru'; // Генерация по ФИО
                $this->command->warn("СГЕНЕРИРОВАН EMAIL: {$email}");
            }

            // Создание пользователя
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $headName,
                    'password' => bcrypt(123),
                ]
            );

            // Создание подразделения
            $unit = Unit::updateOrCreate(
                ['name' => $unitName],
                ['head_id' => $user->id]
            );

            // Создание должности
            $position = Position::firstOrCreate([
                'name' => $positionName,
                'unit_id' => $unit->id,
            ]);

            $user->positions()->syncWithoutDetaching([$position->id]);
            $user->assignRole('user_head');

            $this->command->info("УСПЕШНО: {$unitName} → {$headName} ({$email})");
        }
    }
}

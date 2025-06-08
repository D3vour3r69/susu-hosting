<?php

namespace App\Console\Commands;

use App\Models\Position;
use App\Models\Unit;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class ParseEmployeesCommand extends Command
{
    protected $signature = 'app:parse-employees';
    protected $description = 'Parse employees from susu.ru';

    public function handle()
    {
        $client = new Client();
        $baseUrl = 'https://www.susu.ru';

        $processed = 0;
        $page = 1;
        $maxPages = 9; // Максимум 9 страниц

        do {
            if ($page > $maxPages) {
                $this->info("Достигнут лимит в {$maxPages} страниц, остановка парсинга.");
                break;
            }

            $url = "{$baseUrl}/ru/employee?page={$page}";
            $this->info("Обработка страницы: {$page} - {$url}");

            $response = $client->request('GET', $url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            $profileLinks = $crawler->filter('.item-list .views-field-title-1 a')
                ->each(fn (Crawler $node) => $baseUrl . $node->attr('href'));

            if (empty($profileLinks)) {
                $this->info("Ссылки на профили не найдены на странице {$page}");
                break;
            }

            $this->info("Найдено сотрудников на странице: " . count($profileLinks));
            $bar = $this->output->createProgressBar(count($profileLinks));
            $bar->start();

            foreach ($profileLinks as $profileUrl) {
                try {
                    $this->processProfile($client, $profileUrl);
                } catch (\Exception $e) {
                    $this->error("Ошибка обработки {$profileUrl}: " . $e->getMessage());
                }
                $bar->advance();
            }

            $bar->finish();

            // Исправленный селектор!
            $nextPageLink = $crawler->filter('.pagination li.next a');
            $nextPageExists = $nextPageLink->count() > 0;

            $this->info("Следующая страница: " . ($nextPageExists ? 'Да' : 'Нет'));

            $page++;
        } while ($nextPageExists);

        $this->info("\n✅ Обработано сотрудников: {$processed}");
    }

    private function processProfile(Client $client, string $url)
    {
        $response = $client->request('GET', $url);
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        // Извлечение основных данных
        $name = $crawler->filter('h1.page-header')->text();
        $email = $this->extractEmail($crawler);

        // Создание/обновление пользователя
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(Str::random(16)),
            ]
        );

        // Обработка должностей
        $positions = $crawler->filter('.field-name-field-position-and-department .field-item')
            ->each(fn (Crawler $node) => trim($node->text()));

        foreach ($positions as $positionText) {
            $position = $this->processPosition($positionText);

            if ($position) {
                // Привязка через промежуточную таблицу
                $user->positions()->syncWithoutDetaching([$position->id]);
            }
        }
    }

    private function extractEmail(Crawler $crawler): string
    {
        $emailNode = $crawler->filter('.field-name-field-email .field-item');
        if ($emailNode->count() > 0) {
            $email = trim($emailNode->text());
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $email;
            }
        }

        // Генерация email на основе ФИО
        $name = $crawler->filter('h1.page-header')->text();
        return $this->generateEmail($name);
    }

    private function generateEmail(string $name): string
    {
        $transliterated = Str::ascii($name);
        $parts = array_map('trim', explode(' ', $transliterated));
        $parts = array_filter($parts);

        // Формат: фамилия_ио@susu.ru
        $lastName = Str::slug($parts[0] ?? '');
        $initials = '';

        if (isset($parts[1])) {
            $initials .= mb_substr($parts[1], 0, 1);
        }
        if (isset($parts[2])) {
            $initials .= mb_substr($parts[2], 0, 1);
        }

        return Str::lower("{$lastName}_{$initials}@susu.ru");
    }

    private function processPosition(string $positionText): ?Position
    {
        // Улучшенное извлечение названия кафедры
        if (preg_match('/(?:кафедр[аы]|направления|департамент[а]?)\s+[«"](.+?)[»"]/ui', $positionText, $matches)) {
            $unitName = $this->normalizeUnitName($matches[1]);
            $unit = Unit::where('name', 'like', "%{$unitName}%")->first();

            if ($unit) {
                return Position::firstOrCreate([
                    'unit_id' => $unit->id,
                    'name' => $positionText,
                ]);
            }
        }
        return null;
    }

    private function normalizeUnitName(string $name): string
    {
        return trim(str_replace(['Кафедра', '«', '»', '"', "'"], '', $name));
    }
}

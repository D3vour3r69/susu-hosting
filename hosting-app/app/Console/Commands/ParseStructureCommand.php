<?php

namespace App\Console\Commands;

use App\Models\Position;
use App\Models\Unit;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class ParseStructureCommand extends Command
{
    protected $signature = 'app:parse-structure';
    protected $description = 'Parse departments structure from susu.ru';

    public function handle()
    {
        $client = new Client();
        $response = $client->request('GET', 'https://www.susu.ru/ru/university/official/structure');
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        // Ищем все таблицы с подразделениями напрямую
        $tables = $crawler->filter('table.views-table');

        if ($tables->count() === 0) {
            $this->error("❌ Таблицы с подразделениями не найдены");
            return;
        }

        $this->info("✅ Найдено таблиц: " . $tables->count());

        $bar = $this->output->createProgressBar($tables->count());
        $bar->start();

        $tables->each(function (Crawler $table) use ($bar) {
            $this->processTable($table);
            $bar->advance();
        });

        $bar->finish();
        $this->info("\n✅ Структура подразделений успешно обработана");
    }

    private function processTable(Crawler $table)
    {
        $units = $table->filter('tbody tr')->each(function (Crawler $row) {
            $columns = $row->filter('td');
            if ($columns->count() < 5) return null;

            // Извлекаем должность руководителя
            $positionText = $this->extractPosition($columns->eq(1)->text());

            return [
                'name' => $columns->eq(0)->text(),
//                'head' => $this->extractHeadName($columns->eq(1)->text()),
                'head' => 'Начальник',
                'position' => $positionText,
                'email' => $this->extractEmail($columns->eq(4)),
            ];
        });

        $units = array_filter($units);
        $this->info("\nОбрабатывается таблица с " . count($units) . " подразделениями");

        foreach ($units as $unitData) {
            $this->processUnit($unitData);
        }
    }

    private function processUnit(array $unitData)
    {
        if (!filter_var($unitData['email'], FILTER_VALIDATE_EMAIL)) {
            $this->warn("⚠️ Invalid email: {$unitData['email']} for {$unitData['name']}");
            $unitData['email'] = $this->generateEmail($unitData['name']);
        }
        $normalizedName = $this->normalizeUnitName($unitData['name']);

        // Создание руководителя
        $user = User::firstOrCreate(
            ['email' => $unitData['email']],
            [
                'name' => $unitData['head'],
                'password' => bcrypt(123),
            ]
        );

        $unit = Unit::firstOrCreate(
            ['name' => $normalizedName],
            [
                ['head_id' => $user->id]
            ]
        );

        // Создаем должность для руководителя
        $position = Position::firstOrCreate([
            'name' => $unitData['position'],
            'unit_id' => $unit->id,
        ]);

        // Привязываем пользователя к должности
        $user->positions()->syncWithoutDetaching([$position->id]);
        $user->assignRole('user_head');
        // Связывание подразделения с руководителем
        $unit->head()->associate($user);
        $unit->save();
    }

    // Извлекаем должность руководителя из текста
    private function extractPosition(string $text): string
    {
        // Убираем ФИО и оставляем должность
        $text = preg_replace('/[А-Я][а-я]+\s+[А-Я][а-я]+(?:\s+[А-Я][а-я]+)?/u', '', $text);

        // Убираем лишние символы
        $text = trim(preg_replace('/[,\-–]/u', '', $text));

        // Если текст пустой, используем стандартную должность
        return $text ?: 'Руководитель подразделения';
    }

    private function normalizeUnitName(string $name): string
    {
        // Удаляем лишние слова и символы
        return trim(preg_replace('/Кафедра|«|»|лаборатория|центр|\(.*?\)/u', '', $name));
    }

    private function extractHeadName(string $text): string
    {
        // Извлекаем только ФИО (до первой запятой)
        return trim(explode(',', $text)[0]);
    }

    private function extractEmail(Crawler $column): string
    {
        // Пытаемся найти email в ссылках (приоритетный источник)
        $emailLinks = $column->filter('a[href^="mailto:"]');
        foreach ($emailLinks as $link) {
            $href = $link->getAttribute('href');
            if (preg_match('/^mailto:([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,10})$/i', $href, $matches)) {
                return $matches[1];
            }
        }

        // Поиск во внутреннем тексте элементов
        $textElements = $column->filter('a, div, span, p')->each(function (Crawler $node) {
            return trim($node->text());
        });

        foreach ($textElements as $text) {
            $text = $this->cleanText($text);
            if ($email = $this->findValidEmail($text)) {
                return $email;
            }
        }

        // Поиск во всем тексте колонки
        $fullText = $this->cleanText($column->text());
        if ($email = $this->findValidEmail($fullText)) {
            return $email;
        }

        // Генерация fallback email
        return $this->generateEmail($this->normalizeUnitName($fullText));
    }

    private function cleanText(string $text): string
    {
        // Замена неразрывных пробелов и специальных символов
        $text = str_replace(["\u{00A0}", "[at]", "(at)", "[dot]", "(dot)", " "], [" ", "@", "@", ".", ".", ""], $text);

        // Удаление телефонных номеров (шаблоны вида XXX-XX-XX)
        $text = preg_replace('/\d{2,5}-\d{2,3}-\d{2,4}/', '', $text);

        return trim(preg_replace('/\s+/', ' ', $text));
    }

    private function findValidEmail(string $text): ?string
    {
        preg_match_all('/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,10}/i', $text, $matches);

        foreach ($matches[0] ?? [] as $candidate) {
            // Фильтрация ложных срабатываний (исключаем номера телефонов)
            if (!preg_match('/^\d+$/', explode('@', $candidate)[0]) // не только цифры
                && !preg_match('/-\d/', $candidate) // нет цифр после дефиса
                && filter_var($candidate, FILTER_VALIDATE_EMAIL)
            ) {
                return $candidate;
            }
        }
        return null;
    }

    private function generateEmail(string $name): string
    {
        $transliterated = Str::ascii($name);
        $parts = preg_split('/\s+/', $transliterated);

        // Фильтрация и обработка частей
        $validParts = array_filter($parts, fn($p) => !preg_match('/^\d+$/', $p));
        $lastPart = end($validParts) ?: 'unknown';

        // Очистка от запрещенных символов
        $email = Str::lower(preg_replace('/[^a-z0-9._-]/i', '', $lastPart));

        return $email . '@susu.ru';
    }
}

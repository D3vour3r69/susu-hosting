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
    function mb_ucfirst(string $string, string $encoding = 'UTF-8'): string
    {
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, null, $encoding);

        return mb_strtoupper($firstChar, $encoding) . mb_strtolower($then, $encoding);
    }
    public function run()
    {
        $url = 'https://www.susu.ru/ru/university/official/structure';

        $browser = new HttpBrowser(HttpClient::create());
        $crawler = $browser->request('GET', $url);

        $headsMap = [];
        $targetSections = ['Отделы', 'Высшие школы и институты'];
        $currentSection = null;

        $rows = $crawler->filter('tr');
        foreach ($rows as $domElement) {
            $row = new Crawler($domElement);

            if ($row->filter('td.views-field-title')->count() === 0) {
                continue;
            }

            $unitName = trim($row->filter('td.views-field-title')->text());

            if (in_array($unitName, $targetSections)) {
                $currentSection = $unitName;
                continue;
            }

            if (!in_array($currentSection, $targetSections)) {
                continue;
            }

            $headFull = $row->filter('td.views-field-field-head')->count() ? trim($row->filter('td.views-field-field-head')->text()) : '';
            if (empty($unitName) || empty($headFull) || $headFull === '-') {
                continue;
            }

            $email = null;
            if ($row->filter('td.views-field-field-email')->count()) {
                $emailCell = $row->filter('td.views-field-field-email');
                $divs = $emailCell->filter('div');
                if ($divs->count() > 1) {
                    $emailRaw = trim($divs->eq(1)->text());
                    $emailNormalized = mb_strtolower(str_replace(['[at]', '[dot]', ' '], ['@', '.', ''], $emailRaw));
                    if (preg_match('/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}/i', $emailNormalized, $matches)) {
                        $email = strtolower(trim($matches[0]));
                    }
                }
            }

            if (!$email) {
                $email = Str::random(10) . '@example.com';
                $this->command->warn("У пользователя '{$headFull}' отсутствовал email, сгенерирован: {$email}");
            }

            $parts = explode(',', $headFull, 2);
            $headName = trim($parts[0]);
            $positionName = isset($parts[1]) ? trim($parts[1]) : 'Без должности';
            $positionName = mb_ucfirst($positionName);
//            $headName = preg_replace('/,.*$/u', '', $headFull);
//            $headName = trim($headName);

            if (!isset($headsMap[$email])) {
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $headName,
                        'password' => bcrypt(123),
                    ]
                );
                $headsMap[$email] = $user->id;
            }

            $unit = Unit::updateOrCreate(
                ['name' => $unitName],
                ['head_id' => $headsMap[$email]]
            );

            $position = Position::firstOrCreate(
                [
                    'name' => $positionName,
                    'unit_id' => $unit->id,
                ]
            );

            $user->positions()->syncWithoutDetaching([$position->id]);
            $user->assignRole('user_head');
            $this->command->info("Добавлено подразделение: {$unitName} с главой {$headName} ({$email}), должность: {$positionName}");
        }

        $this->command->info('Парсинг структуры завершён.');
    }
}

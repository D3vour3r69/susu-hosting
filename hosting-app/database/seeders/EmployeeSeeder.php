<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class EmployeeSeeder extends Seeder
{
    private array $replacements = [
        // Общие замены для всех сущностей
        '/^кафедры/u' => 'Кафедра',
        '/^кафедре/u' => 'Кафедра',
        '/^кафедру/u' => 'Кафедра',
        '/^лаборатории/u' => 'Лаборатория',
        '/^факультета/u' => 'Факультет',
        '/^деканат/u' => 'Деканат',
        '/^управления/u' => 'Управление',
        '/^учебного отдела/u' => 'Учебный отдел',
        '/^кафедрой/u' => 'Кафедра',
        '/^сектором/u' => 'Сектор',
        '/^института/u' => 'Институт',
        '/^[aа]дминистраци(я|и)/ui' => 'Администраци$1',
    ];

    // Универсальная функция нормализации
    public function normalizeName(string $rawName): string
    {
        $name = trim($rawName);

        // Применяем все замены из массива
        $name = preg_replace(
            array_keys($this->replacements),
            array_values($this->replacements),
            $name
        );

        return $name;
    }

    public function cleanEmail(string $rawEmail): string
    {

        $email = trim(preg_replace('/^E-mail:\s*/i', '', $rawEmail));

        $email = str_ireplace(['[at]', '[dot]'], ['@', '.'], $email);

        $email = preg_replace('/\s+/u', '', $email);

        return $email;
    }

    public function run()
    {
        $baseUrl = 'https://www.susu.ru';
        $url = $baseUrl.'/ru/employee';

        $browser = new HttpBrowser(HttpClient::create());

        while (true) {
            $this->command->info("Парсим страницу: {$url}");

            $crawler = $browser->request('GET', $url);

            // Получаем ссылки на сотрудников из элементов .views-row a
            $employeeLinks = $crawler->filter('.views-row a')->links();

            $this->command->info('Найдено сотрудников на странице: '.count($employeeLinks));

            foreach ($employeeLinks as $link) {
                $profileUrl = $link->getURI();

                $this->command->info("Парсим сотрудника: {$profileUrl}");

                $profileCrawler = $browser->request('GET', $profileUrl);

                // Получаем ФИО из h1.page-header
                $fullName = $profileCrawler->filter('h1.page-header')->count()
                    ? trim($profileCrawler->filter('h1.page-header')->text())
                    : null;

                if (! $fullName) {
                    $this->command->warn("Не удалось получить ФИО для {$profileUrl}");

                    continue;
                }

                // Получаем email
                $rawEmail = $profileCrawler->filter('.field-name-field-email')->count()
                    ? trim($profileCrawler->filter('.field-name-field-email')->text())
                    : null;

                $email = $rawEmail ? $this->cleanEmail($rawEmail) : null;

                // Парсим должность и подразделения из каждого .field-item внутри .field-name-field-position-and-department
                $posDeptItems = $profileCrawler->filter('.field-name-field-position-and-department .field-item');

                $positions = [];
                $units = [];

                foreach ($posDeptItems as $item) {
                    $itemCrawler = new Crawler($item);

                    // Получаем полный текст (например: "Доцент кафедры «Прикладная математика и программирование»")
                    $fullText = trim($itemCrawler->text());

                    // Получаем текст ссылки (подразделение)
                    $unitName = $itemCrawler->filter('a')->count() ? trim($itemCrawler->filter('a')->text()) : null;

                    // Отделяем должность - вычитаем подразделение из полного текста
                    $positionName = $unitName ? trim(str_replace($unitName, '', $fullText)) : $fullText;

                    if ($positionName) {
                        $positions[] = $this->normalizeName($positionName);
                    }
                    if ($unitName) {
                        $units[] = $this->normalizeName($unitName);
                    }
                }

                // Уникализируем подразделения и должности
                $units = array_unique($units);
                $positions = array_unique($positions);

                // Создаём/находим подразделения
                $unitModels = [];
                foreach ($units as $unitName) {
                    $unitModels[] = Unit::firstOrCreate(['name' => trim($unitName)]);
                }

                // Создаём/находим должности для каждого подразделения
                $positionModels = [];
                foreach ($unitModels as $unitModel) {
                    foreach ($positions as $positionName) {
                        $positionModels[] = Position::firstOrCreate([
                            'name' => $positionName,
                            'unit_id' => $unitModel->id,
                        ]);
                    }
                }

                // Создаём или обновляем пользователя
                $user = User::firstOrCreate(
                    ['name' => $fullName ?? ''],
                    [
                        'email' => $email,
                        'password' => Hash::make('123'), // Задайте пароль по умолчанию или случайный
                    ]
                );
                $user->assignRole('user');

                // Связываем пользователя с должностями (pivot)
                $positionIds = collect($positionModels)->pluck('id')->toArray();
                if (! empty($positionIds)) {
                    $user->positions()->syncWithoutDetaching($positionIds);
                }

                $this->command->info("Сотрудник {$fullName} сохранён с должностями: ".implode(', ', $positions).' и подразделениями: '.implode(', ', $units));
            }

            // Пагинация: ищем ссылку на следующую страницу
            $nextLink = $crawler->filter('ul.pagination li.next a');

            if ($nextLink->count() === 0) {
                $this->command->info('Достигнута последняя страница, парсинг завершён.');
                break;
            }

            $nextHref = $nextLink->attr('href');
            $url = $baseUrl.$nextHref;
        }
    }
}

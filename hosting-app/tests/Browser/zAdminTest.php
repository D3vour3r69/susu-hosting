<?php

namespace Tests\Browser;

use App\Models\Application;
use App\Models\Feature;
use App\Models\Head;
use App\Models\Position;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\HeadsDirectorySeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role; // Добавленный импорт
use Tests\DuskTestCase;

class zAdminTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testAdminFlow()
    {
        $uniqueId = time(); // Уникальный идентификатор для каждого теста

        // Создаем роли
        $adminRole = Role::create(['name' => 'admin']);
        $userHeadRole = Role::create(['name' => 'user_head']);

        // Создаем администратора с уникальным email
        $admin = User::factory()->create([
            'email' => 'admin'.$uniqueId.'@example.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole($adminRole);

        // Создаем руководителя для теста с уникальным email
        $userHead = User::factory()->create([
            'email' => 'user_head'.$uniqueId.'@example.com',
            'password' => bcrypt('password'),
        ]);
        $userHead->assignRole($userHeadRole);

        // Создаем подразделение и позицию с уникальными названиями
        $unit = Unit::create(['name' => 'Тестовый отдел '.$uniqueId]);
        $position = Position::create([
            'unit_id' => $unit->id,
            'name' => 'Тестовая должность '.$uniqueId
        ]);
        $userHead->positions()->attach($position->id);
        $unit->update(['head_id' => $userHead->id]);

        // Создаем технологии
        Feature::factory()->withItems()->create();
        $seeder = new HeadsDirectorySeeder();
        $seeder->run();

        $application = Application::create([
            'user_id' => $userHead->id,
            'unit_id' => $unit->id,
            'head_id' => Head::first()->id,
            'domain' => 'test-app.susu.ru',
            'responsible_id' => $userHead->id,
            'notes' => 'Тестовая записка для админа',
            'status' => 'active',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $application, $uniqueId) {
            // Авторизация админа
            $browser->visit('/login')
                ->typeSlowly('email', $admin->email,10)
                ->typeSlowly('password', 'password',10)
                ->press('Войти')
                ->waitForLocation('/applications')
                ->screenshot('admin-login')
                ->assertSee('Записки для рассмотрения');

            // Принятие записки
            $browser->press("#approve-button-{$application->id}")
                ->screenshot('after-approve')
                ->pause(2500);
            // Проверка в разделе принятых записок
            $browser->visit('/applications/approved')
                ->assertSee("Заявка #{$application->id}")
                ->pause(2500)
                ->screenshot('approved-page');

            // Поиск пользователя
            $browser->visit('/admin/users')
                ->typeSlowly('search', '' . $uniqueId . '@example.com',10)
                ->press('Найти')->pause(1500)
                ->assertSee($uniqueId)
                ->clickLink('Просмотр')
                ->assertSee('Профиль пользователя:')
                ->pause(2500)
                ->screenshot('user-profile');

            // Управление технологиями
            $browser->visit('/features')
                ->screenshot('before-add-category');

            // Добавление новой категории технологий
            $browser->type('name', 'Frontend ' . $uniqueId)
                ->typeSlowly('slug', 'frontend-' . $uniqueId,10)
                ->typeSlowly('description', 'Технологии фронтенд-разработки ' . $uniqueId, 10)
                ->press('Добавить')
                ->waitFor('.feature-item-card', 10)
                ->screenshot('after-add-category');

            // Добавление новой технологии в категорию
            $browser->within('.feature-item-card:last-child', function (Browser $browser) use ($uniqueId) {
                $browser->type('input[name="name"]', 'React ' . $uniqueId)
                    ->typeSlowly('input[name="description"]', 'React framework ' . $uniqueId,10)
                    ->typeSlowly('input[name="slug"]', 'react-' . $uniqueId,10)
                    ->press('Добавить вариант')
                    ->pause(2500);
            })->screenshot('after-add-technology');

            // Удаление технологии
            $browser->within('.feature-item-card:last-child .badge:last-child', function (Browser $browser) {
                $browser->press('button.btn-danger');
            })->pause(1000)
                ->screenshot('after-delete-technology');

            // Удаление категории
            $browser->within('.feature-item-card:last-child .card-header', function (Browser $browser) {
                $browser->press('button.btn-danger');
            })->pause(1000)
                ->screenshot('after-delete-category');
        });
    }
}

<?php

namespace Tests\Browser;

use App\Models\Application;
use App\Models\Feature;
use App\Models\Position;

use App\Models\Unit;
use App\Models\User;
use Database\Seeders\HeadsDirectorySeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role;
use Tests\DuskTestCase;

class UserHeadTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testUserHeadFlow()
    {
        if (!is_dir(base_path('tests/Browser/screenshots'))) {
            mkdir(base_path('tests/Browser/screenshots'), 0777, true);
        }
        $uniqueId = time(); // Уникальный идентификатор для каждого теста

        // Создаем роли
        $adminRole = Role::create(['name' => 'admin']);
        $userHeadRole = Role::create(['name' => 'user_head']);

        // Создаем пользователя и подразделение с уникальными данными
        $unit = Unit::create(['name' => 'Отдел глобальных сетей '.$uniqueId]); // Исправлено
        $user = User::factory()->create([
            'email' => 'user_head'.$uniqueId.'@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole($userHeadRole);

        // Создаем позицию и привязываем пользователя
        $position = Position::create([
            'unit_id' => $unit->id,
            'name' => 'Руководитель проектов '.$uniqueId
        ]);
        $user->positions()->attach($position->id);
        $unit->update(['head_id' => $user->id]);

        // Создаем технологии
        Feature::factory()->withItems()->create();

        $seeder = new HeadsDirectorySeeder();
        $seeder->run();
        $this->browse(function (Browser $browser) use ($user, $uniqueId) {
            // Авторизация
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('Войти')
                ->waitForLocation('/applications')
                ->screenshot('user-head-login')
                ->assertSee('Мои служебные записки');

            // Создание служебной записки
            $browser->visit('/applications/create')
                ->waitFor('#features_select')
                ->select('head_id', 1)
                ->script("$('#features_select').val(['1','2']).trigger('change');");

            $browser->type('domain', 'test'.$uniqueId.'.susu.ru')
                ->waitFor('#responsible_id option')
                ->select('responsible_id', 1)
                ->type('notes', 'Тестовое создание записки '.$uniqueId)
                ->press('Создать записку')
                ->waitForLocation('/applications')
                ->screenshot('after-create-application');

            // Получаем ID созданной записки
            $appId = Application::latest()->first()->id;


            // Удаление записки
            $browser->click("#delete-button-{$appId}")
                ->screenshot('after-delete')
                ->pause(1000);

            $browser->visit('/applications/create')
                ->waitFor('#features_select')
                ->select('head_id', 1)
                ->script("$('#features_select').val(['1','2']).trigger('change');");

            $browser->type('domain', 'test'.$uniqueId.'.susu.ru')
                ->waitFor('#responsible_id option')
                ->select('responsible_id', 1)
                ->type('notes', 'Тестовое создание записки '.$uniqueId)
                ->press('Создать записку')
                ->waitForLocation('/applications')
                ->screenshot('after-create-application');



            // Просмотр раздела по подразделению
            $browser->visit('/applications/unit')
                ->screenshot('before-unit-index')
                ->assertSee('Служебные записки по подразделению')
                ->pause(1000)
                ->screenshot('unit-index');

            $browser->visit('/applications')
                ->assertSee('Мои служебные записки')
                ->pause(3000)
                ->screenshot('my-applications');
            $appId = Application::latest()->first()->id;
            $browser->press("#download-button-{$appId}");
//                ->waitForEvent('download', 10);
//                ->assertDownloaded("application_{$appId}.pdf");

        });
    }
}

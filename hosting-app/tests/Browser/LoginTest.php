<?php

namespace Tests\Browser;

use App\Models\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;
    public function testExample(): void
    {
//        $realFeature = Feature::all();
//        $password = '123';
//        $user = User::create([
//                'name' => 'Зайцев Андрей Владимирович',
//                'email' => 'zaycev@example.com',
//                'password' => bcrypt($password),
//                'role' => 'user_head',
//
//        ]);
        $userHead = User::factory()->userHead()->create([
            'name' => 'Зайцев Андрей Владимирович',
            'email' => 'zaycev@example.com',
        ]);
        $admin = User::factory()->admin()->create();

        $this->browse(function (Browser $browser) use ($userHead) {
            $browser->visit('/login')
                ->pause(1000)
                ->type('email', $userHead->email)
                ->pause(500)
                ->type('password', 'password')
                ->pause(500)
                ->press('Войти')
                ->waitForLocation('/applications')
                ->screenshot('after-login')
                ->pause(1000);

            $browser
                ->click('.nav-link.dropdown-toggle')
                ->waitFor('.dropdown-menu')
                ->clickLink('Профиль')
                ->assertPathIs(route('profile.show', [], false))
                ->screenshot('profile-page')
                ->pause(1000);

            $browser->assertPathIs(route('profile.show', [], false))
                ->screenshot('before-assert')
                ->assertInputValue('name', $userHead->name);

        });
    }
}

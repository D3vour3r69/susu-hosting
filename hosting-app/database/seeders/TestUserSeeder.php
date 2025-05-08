<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
//    public function run() {
//        User::create([
//            'name' => 'Зайцев Андрей Владимирович',
//            'email' => 'zaycev@example.com',
//            'password' => bcrypt('123'),
//        ]);
//    }
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $users = [
            [
                'name' => 'Зайцев Андрей Владимирович',
                'email' => 'zaycev@example.com',
                'password' => bcrypt('123'),
                'role' => 'user_head',
            ],
            [
                'name' => 'Малышев Данил николаевич',
                'email' => 'malyshev@example.com',
                'password' => bcrypt('123'),
                'role' => 'user_head',
            ],
            [
                'name' => 'Леонович Сергей Александрович',
                'email' => 'leonovich@example.com',
                'password' => bcrypt('123'),
                'role' => 'user_head',
            ],
            [
                'name' => 'Савинков Максим Александрович',
                'email' => 'savynkoff@example.com',
                'password' => bcrypt('123'),
                'role' => 'user_head',
            ],
            [
                'name' => 'Админ Админович Админов',
                'email' => 'admin@example.com',
                'password' => bcrypt('123'),
                'role' => 'admin', // Для теста отдельная роль, чтобы каждый раз не создавать админа руками.
            ],
            [
                'name' => 'Райян Гослинг Александрович',
                'email' => 'DaNeUmerOnVKontseDraiva@example.com',
                'password' => bcrypt('123'),
                'role' => 'user_head', // Для теста отдельная роль, чтобы каждый раз не создавать админа руками.
            ],
            [
                'name' => 'Пророк Санбой',
                'email' => 'prorok@example.com',
                'password' => bcrypt('123'),
                'role' => 'user_head', // Для теста отдельная роль, чтобы каждый раз не создавать админа руками.
            ],

        ];

        foreach ($users as $user) {
            $role = $user['role']; // Беру role в переменную
            unset($user['role']); // Убираю role с массива
            //Немного подумал что странно создавать сидер на 1 админа отдельно
            // поэтому сделал через массив и убрал на добавление так как внизу в параметр передаётся весь массив. А role в таблице нет
            $user_roled = User::firstOrCreate(
                ['name' => $user['name']],
                $user
            );
            $user_roled->assignRole($role);
        }
        $this->command->info('Создано ' . count($users) . ' пользователей');
    }
}

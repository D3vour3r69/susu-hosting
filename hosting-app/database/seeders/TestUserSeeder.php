<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
        $users = [
            [
                'name' => 'Зайцев Андрей Владимирович',
                'email' => 'zaycev@example.com',
                'password' => bcrypt('123'),
            ],
            [
                'name' => 'Малышев Данил николаевич',
                'email' => 'malyshev@example.com',
                'password' => bcrypt('123'),

            ],
            [
                'name' => 'Леонович Сергей Александрович',
                'email' => 'leonovich@example.com',
                'password' => bcrypt('123'),

            ],
            [
                'name' => 'Савинков Максим Александрович',
                'email' => 'savynkoff@example.com',
                'password' => bcrypt('123'),

            ],

        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['name' => $user['name']],
                $user
            );
        }

        $this->command->info('Создано ' . count($users) . ' пользователей');
    }
}

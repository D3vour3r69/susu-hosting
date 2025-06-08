<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $users = [
            [
                'name' => 'Админ Админович Админов',
                'email' => 'admin@example.com',
                'password' => bcrypt('123'),

            ],
        ];

        foreach ($users as $user) {


            $user_roled = User::firstOrCreate(
                ['name' => $user['name']],
                $user
            );
            $user_roled->assignRole('admin');
        }
        $this->command->info('Создан ' . count($users) . ' администратор');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'admin',
            'user',
            'user_head',
        ];
        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role]
            );
        }
        $this->command->info('Создано '.count($roles).' ролей');
    }
}

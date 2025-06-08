<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            TestUserSeeder::class,
//            HeadsSeeder::class,
//            EmployeeSeeder::class,
//            UnitSeeder::class,
//            PositionSeeder::class,
//            PositionUserSeeder::class,
            FeatureSeeder::class,
            HeadsDirectorySeeder::class
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run()
    {
        $programming = Feature::create([
            'slug' => 'programming-language',
            'name' => 'Язык программирования',
            'description' => 'Выбор языка программирования'
        ]);

        $programming->items()->createMany([
            ['slug' => 'php', 'name' => 'PHP', 'description' => 'PHP language'],
            ['slug' => 'python', 'name' => 'Python', 'description' => 'Python language']
        ]);

        $database = Feature::create([
            'slug' => 'database',
            'name' => 'База данных',
            'description' => 'Выбор базы данных'
        ]);

        $database->items()->createMany([
            ['slug' => 'mysql', 'name' => 'MySQL', 'description' => 'MySQL database'],
            ['slug' => 'postgresql', 'name' => 'PostgreSQL', 'description' => 'PostgreSQL database']
        ]);
    }
}

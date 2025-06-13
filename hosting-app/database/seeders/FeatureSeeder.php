<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run()
    {
        $programming = Feature::create([
            'slug' => 'programming-language',
            'name' => 'Язык программирования',
            'description' => 'Выбор языка программирования',
        ]);

        $programming->items()->createMany([
            ['slug' => 'php_7.4', 'name' => 'PHP 7.4', 'description' => 'PHP language'],
            ['slug' => 'php_7.2', 'name' => 'PHP 7.2', 'description' => 'PHP language'],
            ['slug' => 'php_8.4.7', 'name' => 'PHP 8.4.7:latest', 'description' => 'PHP language'],
            ['slug' => 'php_8.3', 'name' => 'PHP 8.3', 'description' => 'PHP language'],
            ['slug' => 'php_8.0', 'name' => 'PHP 8.0', 'description' => 'PHP language'],
            ['slug' => 'python_3.8', 'name' => 'Python 3.8', 'description' => 'Python language'],
            ['slug' => 'python_3.9', 'name' => 'Python 3.9', 'description' => 'Python language'],
            ['slug' => 'python_3.10', 'name' => 'Python 3.10', 'description' => 'Python language'],
            ['slug' => 'python_3.11', 'name' => 'Python 3.11', 'description' => 'Python language'],
            ['slug' => 'python_3.12', 'name' => 'Python 3.12', 'description' => 'Python language'],
            ['slug' => 'python_3.13', 'name' => 'Python 3.13:latest', 'description' => 'Python language'],
        ]);

        $database = Feature::create([
            'slug' => 'database',
            'name' => 'База данных',
            'description' => 'Выбор базы данных',
        ]);

        $database->items()->createMany([
            ['slug' => 'mysql', 'name' => 'MySQL', 'description' => 'MySQL database'],
            ['slug' => 'postgresql', 'name' => 'PostgreSQL', 'description' => 'PostgreSQL database'],
        ]);

        $docker = Feature::create([
            'slug' => 'docker',
            'name' => 'Docker',
            'description' => 'Нужен ли докер?',
        ]);

        $docker->items()->createMany([
            ['slug' => 'docker', 'name' => 'Docker', 'description' => 'Изоляционное окружение Docker'],

        ]);
    }
}

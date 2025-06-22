<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestHeadUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Создаем пользователя-руководителя
        $user = User::create([
            'name' => 'Иванов Иван Иванович',
            'email' => 'user_head@example.com',
            'password' => bcrypt('123'),
        ]);

        $user->assignRole('user_head');

        // Создаем подразделение и назначаем руководителя
        $unit = Unit::create([
            'name' => 'Отдел глобальных сетей',
            'head_id' => $user->id // Назначаем пользователя руководителем
        ]);

        // Создаем позицию и привязываем пользователя
        $position = Position::create([
            'unit_id' => $unit->id,
            'name' => 'Руководитель проектов'
        ]);

        $user->positions()->attach($position->id);
    }
}

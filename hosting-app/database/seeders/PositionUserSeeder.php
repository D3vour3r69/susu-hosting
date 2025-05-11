<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = Unit::all();

        foreach ($units as $unit) {
            $headPosition = Position::firstOrCreate([
                'name' => 'Начальник отдела',
                'unit_id' => $unit->id // Привязываем к подразделению
            ]);

            // Назначаем должность руководителю подразделения
            if ($unit->head) {
                $unit->head->positions()->attach($headPosition);
            }}


        // 4. Для остальных пользователей
        $otherUsers = User::whereDoesntHave('positions', function($query) use ($headPosition) {
            $query->where('position_id', $headPosition->id);
        })->get();

        $positions = Position::where('id', '!=', $headPosition->id)->get();

        foreach ($otherUsers as $user) {
            $randomPositions = $positions->random(rand(1, 2))->pluck('id');
            $user->positions()->attach($randomPositions);
        }
    }
}

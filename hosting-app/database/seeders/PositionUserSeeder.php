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
        $units = Unit::with('head')->get();
        $headPositions = [];

        // 1. Создаем позиции руководителей для каждого подразделения
        foreach ($units as $unit) {
            $headPosition = Position::firstOrCreate(
                ['name' => 'Начальник отдела', 'unit_id' => $unit->id],
                ['name' => 'Начальник отдела', 'unit_id' => $unit->id]
            );

            $headPositions[] = $headPosition->id;

            // Назначаем должность руководителю подразделения, если он существует
            if ($unit->head) {
                $unit->head->positions()->syncWithoutDetaching([$headPosition->id]);
            }
        }

        // 2. Получаем ID всех рядовых должностей
        $regularPositions = Position::whereNotIn('id', $headPositions)
            ->pluck('id')
            ->toArray();

        // 3. Обрабатываем обычных пользователей
        User::whereDoesntHave('positions', function($query) use ($headPositions) {
            $query->whereIn('position_id', $headPositions);
        })->chunkById(100, function ($users) use ($regularPositions) {
            foreach ($users as $user) {
                // Выбираем 1-2 случайные должности из доступных
                $randomPositions = collect($regularPositions)
                    ->shuffle()
                    ->take(rand(1, 2))
                    ->toArray();

                $user->positions()->syncWithoutDetaching($randomPositions);
            }
        });
    }
}

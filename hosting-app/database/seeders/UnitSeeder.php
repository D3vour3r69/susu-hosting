<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Заполняем тестовые подразделения
     */
    public function run(): void
    {
        $units = [
            [
                'name' => 'Сектор веб-разработки',
                'head_id' => '1',

            ],
            [
                'name' => 'Сектор экономики',
                'head_id' => '3',
            ],
            [
                'name' => 'HR-отдел',
                'head_id' => '2',
            ],
            [
                'name' => 'DevOps отдел',
                'head_id' => '4',
            ],
            [
                'name' => 'Отдел аналитиков',
                'head_id' => '5',
            ],
            [
                'name'=>'Отдел менеджмента',
                'head_id' =>'6',
            ],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['name' => $unit['name']],
                $unit
            );
        }

        $this->command->info('Создано ' . count($units) . ' подразделений');
    }
}

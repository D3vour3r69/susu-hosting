<?php

namespace Database\Seeders;

use App\Models\Head;
use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HeadsDirectorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            'Отдел глобальных сетевых технологий',
            'Управление информатизации',
            'Отдел цифровой трансформации',
        ];

        $unitIds = [];
        foreach ($units as $unitName) {
            $unit = Unit::firstOrCreate(['name' => $unitName]);
            $unitIds[$unitName] = $unit->id;
        }

        // Ручное заполнение руководителей
        $heads = [
            [
                'full_name' => 'Латухин Дмитрий Викторович',
                'position' => 'Начальник отдела глобальных сетевых технологий',
                'unit_name' => 'Отдел глобальных сетевых технологий',
                'email' => 'latukhindv@susu.ru',
            ],
            [
                'full_name' => 'Подивилова Елена Олеговна',
                'position' => 'Начальник управления информатизации',
                'unit_name' => 'Управление информатизации',
                'email' => 'podivilovaeo@susu.ru',
            ],
            [
                'full_name' => 'Кабиольский Евгений Алексеевич',
                'position' => 'Проректор по цифровой трансформации',
                'unit_name' => 'Отдел цифровой трансформации',
                'email' => 'evgkab@susu.ru',
                ],
        ];

        foreach ($heads as $headData) {
            Head::updateOrCreate(
                [
                    'full_name' => $headData['full_name'],
                    'unit_id' => $unitIds[$headData['unit_name']],
                ],
                [
                    'position' => $headData['position'],
                    'email' => $headData['email'],
                ]
            );
        }

        $this->command->info('Справочник руководителей успешно заполнен!');
    }
}

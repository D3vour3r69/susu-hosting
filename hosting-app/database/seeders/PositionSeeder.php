<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'name' => 'Python junior',
                'unit_id' => '1',
            ],
            [
                'name' => 'Php middle',
                'unit_id' => '1',
            ],
            [
                'name' => 'PHP team lead',
                'unit_id' => '1',
            ],
            [
                'name' => 'Data engineer',
                'unit_id' => 2,
            ],
            [
                'name' => 'Data analytics',
                'unit_id' => 2,
            ],
            [
                'name' => 'Data scientist',
                'unit_id' => 2,
            ],
            [
                'name' => 'HR specialist',
                'unit_id' => 3,
            ],
            [
                'name' => 'DevOps engineer middle',
                'unit_id' => 4,
            ],
            [
                'name' => 'DevOps engineer team lead',
                'unit_id' => 4,
            ],
            [
                'name' => 'DevOps engineer senior',
                'unit_id' => 4,
            ],
            [
                'name' => 'Analyst',
                'unit_id' => 5,
            ],
            [
                'name' => 'Product manager',
                'unit_id' => 6,
            ],
            [
                'name' => 'Manager mean lead',
                'unit_id' => 6,
            ],

        ];
        foreach ($positions as $position) {
            Position::firstOrCreate(
                ['name' => $position['name']],
                $position
            );
        }
        $this->command->info('Создано '.count($positions).' должностей');
    }
}

<?php

namespace Database\Factories;

use App\Models\Feature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feature>
 */
class FeatureFactory extends Factory
{
    public function definition()
    {
        return [
            'slug' => $this->faker->unique()->slug,
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
        ];
    }

    public function withItems()
    {
        return $this->afterCreating(function ($feature) {
            $feature->items()->createMany([
                [
                    'slug' => $this->faker->unique()->slug,
                    'name' => 'PHP 8.3',
                    'description' => 'PHP language'
                ],
                [
                    'slug' => $this->faker->unique()->slug,
                    'name' => 'MySQL',
                    'description' => 'MySQL database'
                ],
                [
                    'slug' => $this->faker->unique()->slug,
                    'name' => 'Docker',
                    'description' => 'Docker containerization'
                ],
            ]);
        });
    }
}

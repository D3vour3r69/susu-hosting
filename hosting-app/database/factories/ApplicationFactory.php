<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Application;


/**
// * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Application::class;
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'notes' => $this->faker->sentence(),
        ];
    }
}

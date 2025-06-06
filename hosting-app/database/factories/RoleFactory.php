<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RoleFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'guard_name' => 'web',
        ];
    }

    public function userHead()
    {
        return $this->state([
            'name' => 'user_head',
        ]);
    }

    public function admin()
    {
        return $this->state([
            'name' => 'admin',
        ]);
    }

}

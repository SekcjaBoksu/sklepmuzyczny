<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    protected $model = \App\Models\Role::class;

    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['admin', 'worker', 'client']),
        ];
    }
}

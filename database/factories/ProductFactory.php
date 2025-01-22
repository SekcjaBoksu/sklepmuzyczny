<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = \App\Models\Product::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'artist' => $this->faker->name,
            'release_date' => $this->faker->date,
            'price' => $this->faker->randomFloat(2, 10, 200),
            'stock' => $this->faker->numberBetween(1, 100),
            'format' => $this->faker->randomElement(['CD', 'Vinyl', 'Special Edition']),
            'category_id' => \App\Models\Category::inRandomOrder()->first()->id,
        ];
    }
}

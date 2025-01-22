<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductTag>
 */
class ProductTagFactory extends Factory
{
    protected $model = \App\Models\ProductTag::class;

    public function definition()
    {
        return [
            'product_id' => \App\Models\Product::inRandomOrder()->first()->id,
            'tag_id' => \App\Models\Tag::inRandomOrder()->first()->id,
        ];
    }
}

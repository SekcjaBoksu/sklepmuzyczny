<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ProductTag;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ProductTagsTableSeeder extends Seeder
{
    public function run()
    {
        Product::all()->each(function ($product) {
            ProductTag::factory(2)->create([
                'product_id' => $product->id,
                'tag_id' => Tag::inRandomOrder()->first()->id,
            ]);
        });
    }
}

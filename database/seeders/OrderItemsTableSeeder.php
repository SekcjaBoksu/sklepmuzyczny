<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderItemsTableSeeder extends Seeder
{
    public function run()
    {
        Order::all()->each(function ($order) {
            OrderItem::factory(3)->create([
                'order_id' => $order->id,
                'product_id' => Product::inRandomOrder()->first()->id,
            ]);
        });
    }
}

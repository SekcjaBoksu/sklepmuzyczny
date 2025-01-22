<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoritesTableSeeder extends Seeder
{
    public function run()
    {
        User::all()->each(function ($user) {
            Favorite::factory(5)->create(['user_id' => $user->id]);
        });
    }
}


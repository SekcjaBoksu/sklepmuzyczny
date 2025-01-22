<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $genres = [
            'Rock', 'Pop', 'Jazz', 'Blues', 'Classical', 'Country', 
            'Electronic', 'Folk', 'Funk', 'Hip-Hop', 'Indie', 'Metal', 
            'Punk', 'R&B', 'Reggae', 'Soul', 'Techno', 'House', 'Disco', 'Alternative'
        ];

        foreach ($genres as $genre) {
            Category::create([
                'name' => $genre,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}


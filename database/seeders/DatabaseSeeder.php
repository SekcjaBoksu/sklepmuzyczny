<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Uruchamianie wszystkich seedów
        $this->call([
            UserSeeder::class,
            RolesTableSeeder::class,
            CategoriesTableSeeder::class,
            ProductsTableSeeder::class,
            //OrdersTableSeeder::class,
            //OrderItemsTableSeeder::class,
            //ReviewsTableSeeder::class,
            //FavoritesTableSeeder::class,
            TagsTableSeeder::class,
            ProductTagsTableSeeder::class,
            UserRolesTableSeeder::class,
        ]);

        // Tworzenie testowego użytkownika
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // Hasło: password
        ]);
    }
}

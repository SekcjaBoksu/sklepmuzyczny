<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Dodanie bazowego admina
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Dodanie bazowego pracownika
        User::updateOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'Employee',
                'password' => Hash::make('password'),
                'role' => 'employee',
            ]
        );

        // Dodanie bazowego klienta
        User::updateOrCreate(
            ['email' => 'client@example.com'],
            [
                'name' => 'Client',
                'password' => Hash::make('password'),
                'role' => 'client',
            ]
        );
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\UserRole;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class UserRolesTableSeeder extends Seeder
{
    public function run()
    {
        User::all()->each(function ($user) {
            UserRole::create([
                'user_id' => $user->id,
                'role_id' => Role::inRandomOrder()->first()->id,
            ]);
        });
    }
}

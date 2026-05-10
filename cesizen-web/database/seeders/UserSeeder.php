<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::truncate();

        User::create([
            'role_id' => 1,
            'name' => 'Admin',
            'email' => 'admin@cesizen.fr',
            'active' => 1,
            'password' => bcrypt('password'),
        ]);

        User::create([
            'role_id' => 2,
            'name' => 'Utilisateur Test',
            'email' => 'user@cesizen.fr',
            'active' => 1,
            'password' => bcrypt('password'),
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin CESIZen',
            'email' => 'admin@cesizen.fr',
            'password' => bcrypt('password'), // Le mot de passe sera "password"
        ]);
    }
}

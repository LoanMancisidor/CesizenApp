<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::insert([
            ['id' => 1, 'libelle' => 'Administrateur'],
            ['id' => 2, 'libelle' => 'Utilisateur'],
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \App\Models\Role::create(['libelle' => 'Administrateur']);
        \App\Models\Role::create(['libelle' => 'Utilisateur']);
    }
}

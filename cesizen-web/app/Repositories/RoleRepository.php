<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;

class RoleRepository
{
    public function getAllWithUsers()
    {
        return Role::with('users')->get();
    }

    public function findByLibelle(string $libelle)
    {
        return Role::where('libelle', $libelle)->first();
    }

    public function create(array $data)
    {
        return Role::create($data);
    }

    public function deleteAndReassign(Role $role)
    {
        $roleParDefaut = $this->findByLibelle('Utilisateur');
        if (!$roleParDefaut) {
            return false;
        }

        User::where('role_id', $role->id)->update(['role_id' => $roleParDefaut->id]);
        return $role->delete();
    }
}

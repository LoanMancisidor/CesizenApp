<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function createUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => 2,
            'active' => 1,
        ]);
    }

    public function updatePassword($user, $newPassword)
    {
        return $user->update([
            'password' => Hash::make($newPassword)
        ]);
    }
}
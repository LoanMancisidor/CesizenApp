<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    /**
     * Get all users with their roles
     */
    public function getAllUsers()
    {
        return User::with('role')->get();
    }

    /**
     * Get all roles
     */
    public function getAllRoles()
    {
        return Role::all();
    }

    /**
     * Create a new user
     */
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => $data['role_id'],
            'password' => Hash::make($data['password']),
            'active' => true,
        ]);
    }

    /**
     * Update an existing user
     */
    public function updateUser(User $user, array $data): User
    {
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role_id = $data['role_id'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return $user;
    }

    /**
     * Delete a user
     */
    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Toggle user active status
     */
    public function toggleUserStatus(User $user): User
    {
        $user->active = !$user->active;
        $user->save();

        return $user;
    }

    /**
     * Find user by id
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}

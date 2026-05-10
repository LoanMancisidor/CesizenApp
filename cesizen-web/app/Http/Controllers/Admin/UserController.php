<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userRepository;

    /**
     * Injection du Repository via le constructeur
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $users = $this->userRepository->getAllUsers();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = $this->userRepository->getAllRoles();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|confirmed|min:8',
        ]);

        $this->userRepository->createUser($request->all());

        return redirect()->route('users.index')->with('success', 'Utilisateur créé !');
    }

    public function edit(User $user)
    {
        $roles = $this->userRepository->getAllRoles();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Logique de contrôle (Validation spécifique)
        if ($user->id === auth()->id() && $request->role_id != $user->role_id) {
            return back()->withErrors(['role_id' => 'Vous ne pouvez pas modifier votre propre rôle.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Délégation de la mise à jour au Repository
        $this->userRepository->updateUser($user, $request->all());

        return redirect()->route('users.index')->with('success', 'Compte mis à jour !');
    }

    public function destroy(User $user)
    {
        $this->userRepository->deleteUser($user);
        return response()->json(['success' => true]);
    }

    public function toggleStatus(User $user)
    {
        $updatedUser = $this->userRepository->toggleUserStatus($user);

        $status = $updatedUser->active ? 'activé' : 'désactivé';
        return back()->with('success', "Le compte de {$updatedUser->name} a été {$status}.");
    }
}

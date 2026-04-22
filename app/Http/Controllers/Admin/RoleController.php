<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleRepository;

    /**
     * Injection du RoleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function index()
    {
        $roles = $this->roleRepository->getAllWithUsers();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|unique:roles|max:255',
        ]);

        $this->roleRepository->create($request->all());

        return redirect()->route('roles.index')->with('success', 'Rôle créé avec succès !');
    }

    public function destroy(Role $role)
    {
        // 1. Protection des rôles système (Logique de contrôle)
        if (in_array($role->libelle, ['Administrateur', 'Utilisateur'])) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer un rôle système.'
            ], 403);
        }

        $deleted = $this->roleRepository->deleteAndReassign($role);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur : Le rôle "Utilisateur" par défaut est introuvable.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Rôle supprimé et utilisateurs reclassés en "Utilisateur".'
        ]);
    }
}

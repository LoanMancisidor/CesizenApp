<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User; // Import du modèle User nécessaire
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('users')->get(); // On charge les users pour le pluck() dans la vue
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

        Role::create($request->all());

        return redirect()->route('roles.index')->with('success', 'Rôle créé avec succès !');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->libelle, ['Administrateur', 'Utilisateur'])) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer un rôle système.'
            ], 403);
        }

        $roleParDefaut = Role::where('libelle', 'Utilisateur')->first();

        if (!$roleParDefaut) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur : Le rôle "Utilisateur" par défaut est introuvable.'
            ], 500);
        }

        // Reclassement des utilisateurs liés au rôle supprimé
        User::where('role_id', $role->id)->update(['role_id' => $roleParDefaut->id]);

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rôle supprimé et utilisateurs reclassés en "Utilisateur".'
        ]);
    }
}

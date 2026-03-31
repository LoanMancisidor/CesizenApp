@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="header-title">
        <h2 style="margin: 0;">Gestion des Utilisateurs</h2>
        <p style="color: #718096; margin: 5px 0 0 0; font-size: 0.9rem;">
            Gérez les accès et les comptes de la plateforme.
        </p>
    </div>
    <div class="header-actions">
        <a href="{{ route('users.create') }}" class="btn-action">+ Créer un utilisateur</a>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Date d'inscription</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td><strong>{{ $user->name }}</strong></td>
                <td>{{ $user->email }}</td>
                <td>
                    <span class="role-badge {{ $user->role && $user->role->libelle === 'Administrateur' ? 'role-admin' : 'role-user' }}">
                        {{ $user->role ? $user->role->libelle : 'N/A' }}
                    </span>
                </td>
                <td>
                    @if($user->active)
                        <span class="role-badge role-user">Actif</span>
                    @else
                        <span class="role-badge" style="background: #fed7d7; color: #c53030;">Inactif</span>
                    @endif
                </td>
                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                <td style="text-align: right; display: flex; justify-content: flex-end; align-items: center; gap: 10px;">
                    {{-- Bouton Activer/Désactiver --}}
                    {{-- On empêche aussi de se désactiver soi-même pour ne pas perdre l'accès --}}
                    @if($user->id !== auth()->id())
                        <form action="{{ route('users.toggle', $user->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="btn-action {{ $user->active ? 'btn-toggle-off' : 'btn-toggle-on' }}" 
                                    style="padding: 6px 12px; font-size: 0.75rem; box-shadow: none;">
                                {{ $user->active ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('users.edit', $user->id) }}" class="btn-edit" style="text-decoration: none; font-size: 0.85rem; color: var(--medium-green); font-weight: 600;">Modifier</a>
                    
                    {{-- Suppression : Uniquement si ce n'est pas mon propre compte --}}
                    @if($user->id !== auth()->id())
                        <button type="button" 
                                class="btn-delete" 
                                style="font-size: 0.85rem; border: none; background: none; cursor: pointer;"
                                data-url="{{ route('users.destroy', $user->id) }}" 
                                onclick="deleteManager.openModal(this)">
                            Supprimer
                        </button>
                    @else
                        <span style="font-size: 0.75rem; color: #a0aec0; font-style: italic; width: 80px; text-align: center;">(Votre compte)</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@include('components.delete-modal')
@endsection
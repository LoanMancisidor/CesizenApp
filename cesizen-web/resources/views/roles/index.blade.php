@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="header-title">
            <h2 style="margin: 0;">Gestion des Rôles</h2>
            <p style="color: #718096; margin: 5px 0 0 0; font-size: 0.9rem;">
                {{ $roles->count() }} rôle(s) défini(s) dans le système.
            </p>
        </div>

        <div class="header-actions">
            <a href="{{ route('roles.create') }}" class="btn-action">+ Ajouter un rôle</a>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Libellé du rôle</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>#{{ $role->id }}</td>
                        <td>
                            <span class="role-badge {{ $role->libelle === 'Administrateur' ? 'role-admin' : 'role-user' }}">
                                {{ $role->libelle }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            {{-- On évite de supprimer les rôles de base pour ne pas casser la DB --}}
                            @if (!in_array($role->libelle, ['Administrateur', 'Utilisateur']))
                                <button type="button" class="btn-delete" data-url="{{ route('roles.destroy', $role->id) }}"
                                    data-users="{{ $role->users->pluck('name')->implode(', ') }}">
                                    Supprimer
                                </button>
                            @else
                                <small style="color: #a0aec0; font-style: italic;">Système</small>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('components.delete-modal')
@endsection

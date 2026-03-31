@extends('layouts.app')

@section('content')
<div class="form-container card">
    <h2>Créer un utilisateur</h2>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <label for="name">Nom complet</label>
        <input type="text" name="name" id="name" placeholder="Ex: Jean Dupont" required>

        <label for="email">Adresse Email</label>
        <input type="email" name="email" id="email" placeholder="exemple@cesi.fr" required>

        <label for="role_id">Rôle attribué</label>
        <select name="role_id" id="role_id" required>
            <option value="">-- Sélectionner un rôle --</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->libelle }}</option>
            @endforeach
        </select>

        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" required>

        <label for="password_confirmation">Confirmer le mot de passe</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>

        <button type="submit" class="btn-submit">Créer le compte</button>
        <a href="{{ route('users.index') }}" class="btn-cancel" style="display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #a0aec0;">Annuler</a>
    </form>
</div>
@endsection
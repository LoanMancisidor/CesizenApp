@extends('layouts.app')

@section('content')
    <div class="form-container card">
        <h2>Modification de l'utilisateur : {{ $user->name }}</h2>

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <label for="name">Nom complet</label>
            <input type="text" name="name" id="name" value="{{ $user->name }}" required>

            <label for="email">Adresse Email</label>
            <input type="email" name="email" id="email" value="{{ $user->email }}" required>

            <label for="role_id">Rôle attribué</label>
            <select name="role_id" id="role_id" required {{ $user->id === auth()->id() ? 'disabled' : '' }}
                style="{{ $user->id === auth()->id() ? 'background-color: #edf2f7; cursor: not-allowed;' : '' }}">
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                        {{ $role->libelle }}
                    </option>
                @endforeach
            </select>

            @if ($user->id === auth()->id())
                <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                <p style="font-size: 0.8rem; color: #718096; margin-top: 5px;">
                    ℹ️ Vous ne pouvez pas modifier votre propre rôle.
                </p>
            @endif

            <div
                style="margin-top: 20px; padding: 10px; background: #edf2f7; border-radius: 8px; font-size: 0.8rem; color: #4a5568;">
                Laissez les champs mot de passe vides pour ne pas le modifier.
            </div>

            <label for="password">Nouveau mot de passe (optionnel)</label>
            <input type="password" name="password" id="password">

            <label for="password_confirmation">Confirmer le nouveau mot de passe</label>
            <input type="password" name="password_confirmation" id="password_confirmation">

            <button type="submit" class="btn-submit">Mettre à jour le compte</button>
            <a href="{{ route('users.index') }}" class="btn-cancel"
                style="display: block; text-align: center; margin-top: 15px; text-decoration: none; color: #a0aec0;">Annuler</a>
        </form>
    </div>
@endsection

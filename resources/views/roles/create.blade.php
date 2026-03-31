@extends('layouts.app')

@section('content')
<div class="content-header">
    <h1>Créer un nouveau rôle</h1>
    <a href="{{ route('roles.index') }}" class="btn-action" style="background: #718096;">Retour</a>
</div>

<div class="card form-container">
    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="libelle">Nom du rôle (ex: Administrateur, Utilisateur)</label>
            <input type="text" name="libelle" id="libelle" placeholder="Saisissez le libellé..." required>
        </div>

        <button type="submit" class="btn-submit">Enregistrer le rôle</button>
    </form>
</div>
@endsection
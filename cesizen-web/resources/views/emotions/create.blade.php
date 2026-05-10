@extends('layouts.app')
@section('content')
<div class="form-container card">
    <h2>Ajouter une émotion</h2>
    <form action="{{ route('emotions.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="nom">Nom de l'émotion</label>
        <input type="text" name="nom" id="nom" placeholder="Ex: Sérénité, Colère..." required>

        <label for="parent_id">Émotion Parente (Optionnel)</label>
            <select name="parent_id" id="parent_id">
                <option value="">-- C'est une émotion de base --</option>
                @foreach($emotionsBase as $base)
                    <option value="{{ $base->id }}">{{ $base->nom }}</option>
                @endforeach
            </select>

        <label for="image_icone">Icône (Image)</label>
        <input type="file" name="image_icone" id="image_icone" accept="image/*">

        <button type="submit" class="btn-submit">Enregistrer l'émotion</button>
        <a href="{{ route('emotions.index') }}" class="btn-cancel">Annuler</a>
    </form>
</div>
@endsection
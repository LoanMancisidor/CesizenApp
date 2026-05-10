@extends('layouts.app')

@section('content')
<div class="form-container card">
    <h2>Modifier l'émotion : {{ $emotion->nom }}</h2>    
    <form action="{{ route('emotions.update', $emotion->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="nom">Nom de l'émotion</label>
        <input type="text" name="nom" id="nom" value="{{ $emotion->nom }}" required>

        <label for="parent_id">Émotion Parente (Optionnel)</label>
        <select name="parent_id" id="parent_id">
            <option value="">-- C'est une émotion de base --</option>
            @foreach($emotionsBase as $base)
                <option value="{{ $base->id }}" {{ $emotion->parent_id == $base->id ? 'selected' : '' }}>
                    {{ $base->nom }}
                </option>
            @endforeach
        </select>

        <label for="image_icone">Changer l'icône</label>
        @if($emotion->image_icone)
            <div class="current-image-preview">
                <p style="font-size: 0.8rem; color: #718096;">Icône actuelle :</p>
                <img src="{{ asset('storage/' . $emotion->image_icone) }}" class="edit-preview-img" style="width: 60px; height: 60px;">
            </div>
        @endif
        <input type="file" name="image_icone" id="image_icone" accept="image/*">

        <button type="submit" class="btn-submit">Mettre à jour</button>
        <a href="{{ route('emotions.index') }}" class="btn-cancel">Annuler</a>
    </form>
</div>
@endsection
@extends('layouts.app')

@section('content')
    <div class="form-container">
        <div class="card">
            <h2 style="margin-top: 0; color: var(--primary-green);">Modifier l'article</h2>
            <form action="{{ route('articles.update', $article->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <label for="titre">Titre de l'article</label>
                <input type="text" name="titre" id="titre" value="{{ $article->titre }}" required>

                <label for="contenu">Contenu détaillé (optionnel)</label>
                <textarea name="contenu" id="contenu" rows="8">{{ $article->contenu }}</textarea>

                <label>Aperçu visuel actuel</label>
                <div class="current-image-preview">
                    @if ($article->image_url)
                        <img src="{{ asset('storage/' . $article->image_url) }}" alt="Aperçu" class="edit-preview-img">
                    @else
                        <div class="no-image-placeholder">
                            <span>Aucune image associée à cet article</span>
                        </div>
                    @endif
                </div>

                <label for="image" class="file-label">Remplacer l'image</label>
                <input type="file" name="image" id="image" accept="image/*" class="file-input">

                <button type="submit" class="btn-submit">Mettre à jour l'article</button>

                <a href="{{ route('articles.index') }}" class="btn-cancel">Annuler les modifications</a>
            </form>
        </div>
    </div>
@endsection

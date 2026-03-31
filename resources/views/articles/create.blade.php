@extends('layouts.app')

@section('content')
<div class="form-container">
    <div class="card">
        <h2 style="margin-top: 0; color: var(--primary-green);">Nouvel Article</h2>
        <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <label for="titre">Titre de l'article</label>
            <input type="text" name="titre" id="titre" placeholder="Donnez un titre à votre article" required>

            <label for="contenu">Contenu détaillé</label>
            <textarea name="contenu" id="contenu" rows="8" placeholder="Écrivez votre article ici..."></textarea>

            <label for="image">Image</label>
            <input type="file" name="image" id="image" accept="image/*">

            <button type="submit" class="btn-submit">Publier l'article</button>
            
            <a href="{{ route('articles.index') }}" style="display: block; text-align: center; margin-top: 15px; color: #A0AEC0; text-decoration: none; font-size: 0.9rem;">Annuler et revenir</a>
        </form>
    </div>
</div>
@endsection
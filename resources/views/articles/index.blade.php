@extends('layouts.app')

@section('content')
    <div class="content-header">
        <a href="{{ route('articles.create') }}" class="btn-action">+ Ajouter un article</a>
        <h2 style="margin: 0;">Gestion des articles</h2>
    </div>

    <div class="articles-grid">
        @foreach($articles as $article)
            <div class="article-card">
                @if($article->image_url)
                    <img src="{{ asset('storage/' . $article->image_url) }}" class="article-thumb" alt="">
                @else
                    <div class="no-image">Pas d'image</div>
                @endif

                <div class="article-body">
                    <p class="article-title">{{ $article->titre }}</p>
                    
                    <div class="article-actions">
                        <a href="{{ route('articles.edit', $article->id) }}" class="btn-edit">Modifier</a>

                        <button type="button" 
                                class="btn-delete" 
                                data-url="{{ route('articles.destroy', $article->id) }}" 
                                onclick="openDeleteModal(this)">
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div id="custom-modal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-icon">⚠️</div>
            <h3>Confirmer la suppression</h3>
            <p>Êtes-vous sûr de vouloir supprimer cet article ?</p>
            <div class="modal-footer">
                <button onclick="closeModal()" class="btn-secondary">Annuler</button>
                <button id="confirm-delete-btn" class="btn-danger-modal">Supprimer</button>
            </div>
        </div>
    </div>
@endsection

<script>
let currentDeleteUrl = '';
let currentCard = null;

// Ouvre la modale personnalisée
function openDeleteModal(button) {
    currentDeleteUrl = button.getAttribute('data-url');
    currentCard = button.closest('.article-card');

    // Affiche l'overlay au clic sur supprimer
    document.getElementById('custom-modal').style.display = 'flex';
}

// Cette fonction ferme la modale
function closeModal() {
    document.getElementById('custom-modal').style.display = 'none';
}

// Clic sur le bouton "Supprimer" de la MODALE
document.addEventListener('DOMContentLoaded', function() {
    const confirmBtn = document.getElementById('confirm-delete-btn');
    
    confirmBtn.addEventListener('click', function() {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(currentDeleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                closeModal();
                currentCard.style.transition = "all 0.5s ease";
                currentCard.style.opacity = "0";
                currentCard.style.transform = "scale(0.8)";
                setTimeout(() => currentCard.remove(), 500);
            } else {
                alert("Erreur lors de la suppression.");
            }
        })
        .catch(error => console.error('Erreur:', error));
    });
});
</script>
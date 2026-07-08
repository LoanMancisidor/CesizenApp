@extends('layouts.app')

@section('content')
    <div class="content-header">
        {{-- Zone de gauche : Titre et Stats rapides --}}
        <div class="header-title">
            <h2 style="margin: 0;">Gestion des Articles</h2>
            <p style="color: #718096; margin: 5px 0 0 0; font-size: 0.9rem;">
                <span id="article-count">{{ $articles->count() }}</span> article(s) publié(s) sur la plateforme.
            </p>
        </div>

        {{-- Zone de droite : Bouton d'action --}}
        <div class="header-actions">
            <a href="{{ route('articles.create') }}" class="btn-action">+ Ajouter un article</a>
        </div>
    </div>

    <div class="articles-grid">
        @foreach ($articles as $article)
            <div class="article-card">
                {{-- Badge de date discret --}}
                <div
                    style="position: absolute; top: 10px; left: 10px; background: rgba(255,255,255,0.9); padding: 4px 8px; border-radius: 8px; font-size: 0.65rem; font-weight: 700; color: var(--primary-green); z-index: 10; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    {{ $article->created_at->format('d/m/Y') }}
                </div>

                @if ($article->image_url)
                    <img src="{{ asset('storage/' . $article->image_url) }}" class="article-thumb"
                        alt="{{ $article->titre }}">
                @else
                    <div class="no-image">
                        <span style="font-size: 2rem;">📝</span>
                        <p style="margin-top: 10px; font-size: 0.8rem;">Pas d'image</p>
                    </div>
                @endif

                <div class="article-body">
                    <h3 class="article-title"
                        style="min-height: 2.4rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $article->titre }}
                    </h3>

                    <div class="article-actions"
                        style="margin-top: 15px; border-top: 1px solid #f1f5f9; padding-top: 15px; gap: 15px;">
                        <a href="{{ route('articles.edit', $article->id) }}" class="btn-edit" style="margin:0;">Modifier</a>

                        <button type="button" class="btn-delete" data-url="{{ route('articles.destroy', $article->id) }}">
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @include('components.delete-modal')
@endsection

@extends('layouts.app')

@section('content')
    <div class="content-header">
        {{-- Zone de gauche : Titre et Fil d'Ariane --}}
        <div class="header-title">
            @isset($parentNom)
                <nav style="margin-bottom: 8px;">
                    <a href="{{ route('emotions.index') }}" style="color: var(--primary-green); text-decoration: none; font-size: 0.9rem; font-weight: 600;">
                        ← Retour aux émotions de base
                    </a>
                </nav>
                <h2 style="margin: 0;">Sous-émotions de : <span style="color: var(--primary-green);">{{ $parentNom }}</span></h2>
            @else
                <h2 style="margin: 0;">Gestion des Émotions de base</h2>
                <p style="color: #718096; margin: 5px 0 0 0; font-size: 0.9rem;">Niveau 1 : Émotions principales du tracker.</p>
            @endisset
        </div>

        {{-- Zone de droite : Bouton d'action --}}
        <div class="header-actions">
            <a href="{{ route('emotions.create') }}" class="btn-action">+ Ajouter une émotion</a>
        </div>
    </div>

    <div class="articles-grid">
        @forelse($emotions as $emotion)
            <div class="article-card" style="text-align: center; padding: 25px;">
                {{-- Badge de niveau discret --}}
                <div style="position: absolute; top: 15px; right: 15px; background: #f7fafc; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; color: #a0aec0; border: 1px solid #edf2f7;">
                    Niveau {{ $emotion->niveau }}
                </div>

                <div class="stat-icon" style="background: #f0fff4; margin: 0 auto 15px auto; font-size: 2.2rem; width: 70px; height: 70px; border-radius: 50%;">
                    @if($emotion->image_icone)
                        <img src="{{ asset('storage/' . $emotion->image_icone) }}" style="width: 45px; height: 45px; object-fit: contain;">
                    @endif
                </div>

                <h3 style="margin-bottom: 10px; font-size: 1.3rem;">{{ $emotion->nom }}</h3>
                
                @if($emotion->niveau == 1)
                    <div style="margin-bottom: 20px;">
                        <a href="{{ route('emotions.parent', $emotion->id) }}" 
                        class="btn-action" 
                        style="display: inline-block; font-size: 0.75rem; padding: 8px 16px;">
                            Voir les émotions secondaires ({{ $emotion->enfants()->count() }})
                        </a>
                    </div>
                @endif
                
                <div class="article-actions" style="justify-content: center; margin-top: 10px; border-top: 1px solid #f1f5f9; padding-top: 20px; gap: 20px;">
                    <a href="{{ route('emotions.edit', $emotion->id) }}" class="btn-edit" style="margin:0;">Modifier</a>
                    <button type="button" 
                            class="btn-delete" 
                            data-url="{{ route('emotions.destroy', $emotion->id) }}" 
                            {{-- On récupère les noms des enfants --}}
                            data-children="{{ $emotion->enfants->pluck('nom')->implode(', ') }}"
                            onclick="deleteManager.openModal(this)">
                        Supprimer
                    </button>
                </div>
            </div>
        @empty
            <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                <p style="color: #a0aec0;">Aucune émotion trouvée pour le moment.</p>
            </div>
        @endforelse
    </div>

    @include('components.delete-modal')
@endsection
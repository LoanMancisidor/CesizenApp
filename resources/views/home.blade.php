@extends('layouts.app')

@section('content')
<div class="dashboard-header">
    <h2 style="margin-bottom: 5px;">Bonjour, {{ auth()->user()->name }} 👋</h2>
    <p style="color: #718096; margin-top: 0;">Voici l'état actuel de la plateforme CESIZen.</p>
</div>

<div class="stats-grid">
    {{-- Utilisateurs avec indicateur d'activité --}}
    <div class="card stat-card">
        <div class="stat-icon" style="background: #e3f9eb; color: var(--primary-green);">👥</div>
        <div class="stat-info">
            <span class="stat-label">Utilisateurs</span>
            <span class="stat-number">{{ $stats['users_count'] }}</span>
            <small style="color: #48bb78;">{{ $stats['users_active'] }} actifs</small>
        </div>
    </div>

    {{-- Articles --}}
    <div class="card stat-card">
        <div class="stat-icon" style="background: #fff5f5; color: var(--red);">📚</div>
        <div class="stat-info">
            <span class="stat-label">Articles</span>
            <span class="stat-number">{{ $stats['articles_count'] }}</span>
            <small style="color: #718096;">Ressources bien-être</small>
        </div>
    </div>

    {{-- Émotions (Dynamique !) --}}
    <div class="card stat-card">
        <div class="stat-icon" style="background: #ebf4ff; color: #3182ce;">🌈</div>
        <div class="stat-info">
            <span class="stat-label">Référentiel Émotions</span>
            <span class="stat-number">{{ $stats['emotions_count'] }}</span>
            <small style="color: #718096;">Configurées pour le tracker</small>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 30px;">
    
    {{-- Colonne Gauche : Dernier Article --}}
    <div class="card">
        <h3 style="margin-top: 0; font-size: 1.1rem; border-bottom: 1px solid #edf2f7; padding-bottom: 10px;">Dernière publication</h3>
        @if($stats['last_article'])
            <div style="margin-top: 15px;">
                <p style="font-weight: 600; color: var(--dark-text); margin-bottom: 5px;">{{ $stats['last_article']->titre }}</p>
                <p style="font-size: 0.85rem; color: #718096;">Publié {{ $stats['last_article']->created_at->diffForHumans() }}</p>
                <a href="{{ route('articles.edit', $stats['last_article']->id) }}" style="color: var(--primary-green); font-size: 0.85rem; text-decoration: none; font-weight: 600;">Modifier l'article →</a>
            </div>
        @else
            <p style="color: #A0AEC0;">Aucun article pour le moment.</p>
        @endif
    </div>

    {{-- Colonne Droite : Inscriptions Récentes --}}
    <div class="card">
        <h3 style="margin-top: 0; font-size: 1.1rem; border-bottom: 1px solid #edf2f7; padding-bottom: 10px;">Derniers inscrits</h3>
        <ul style="list-style: none; padding: 0; margin-top: 15px;">
            @foreach($stats['recent_users'] as $u)
                <li style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f7fafc;">
                    <span style="font-size: 0.9rem; font-weight: 500;">{{ $u->name }}</span>
                    <span class="role-badge" style="font-size: 0.7rem; padding: 2px 8px;">{{ $u->created_at->diffForHumans(null, true) }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
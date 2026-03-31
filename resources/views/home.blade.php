@extends('layouts.app')

@section('content')
<div class="dashboard-header">
    <h2 style="margin-bottom: 5px;">Bonjour, Administrateur 👋</h2>
    <p style="color: #718096; margin-top: 0;">Voici l'état actuel de la plateforme CESIZen.</p>
</div>

<div class="stats-grid">
    <div class="card stat-card">
        <div class="stat-icon" style="background: #e3f9eb; color: var(--primary-green);">👥</div>
        <div class="stat-info">
            <span class="stat-label">Utilisateurs</span>
            <span class="stat-number">{{ $stats['users_count'] }}</span>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon" style="background: #fff5f5; color: var(--red);">📚</div>
        <div class="stat-info">
            <span class="stat-label">Articles</span>
            <span class="stat-number">{{ $stats['articles_count'] }}</span>
        </div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon" style="background: #ebf4ff; color: #3182ce;">🌈</div>
        <div class="stat-info">
            <span class="stat-label">Émotions</span>
            <span class="stat-number">8</span>
        </div>
    </div>
</div>

<div class="dashboard-footer" style="margin-top: 40px;">
    <div class="card">
        <h3 style="margin-top: 0;">Dernière activité</h3>
        @if($stats['last_article'])
            <p>Dernier article : <strong>{{ $stats['last_article']->titre }}</strong> 
            <small style="color: #A0AEC0;">({{ $stats['last_article']->created_at->diffForHumans() }})</small></p>
        @else
            <p>Aucun article publié pour le moment.</p>
        @endif
    </div>
</div>
@endsection
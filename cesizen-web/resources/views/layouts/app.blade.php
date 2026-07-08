<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CESIZen - Admin</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/delete-handler.js', 'resources/js/user-menu.js'])
</head>

<body>

    <header class="header-banner">
        <a href="{{ route('home') }}" class="brand-link">
            <h1>CESIZen Admin</h1>
        </a>

        <nav class="nav-menu">
            <a href="{{ route('articles.index') }}"
                class="nav-link {{ request()->is('admin/articles*') ? 'active' : '' }}">Articles</a>
            <a href="{{ route('emotions.index') }}"
                class="nav-link {{ request()->is('admin/emotions*') ? 'active' : '' }}">Émotions</a>
            <a href="{{ route('users.index') }}"
                class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">Utilisateurs</a>
            <a href="{{ route('roles.index') }}"
                class="nav-link {{ request()->is('admin/roles*') ? 'active' : '' }}">Rôles</a>
            <div class="user-menu-container nav-right">
                <div class="user-profile-trigger">
                    <div class="user-avatar">
                        👤
                    </div>
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <i class="arrow-down"></i>
                </div>

                <div id="user-dropdown" class="user-dropdown">
                    {{-- On force la couleur verte ici avec style --}}
                    <a href="{{ route('users.edit', auth()->id()) }}" class="dropdown-item"
                        style="color: var(--primary-green); font-weight: 600;">
                        <span class="icon">👤</span> Mon profil
                    </a>

                    <div class="dropdown-divider"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item logout-button">
                            <span class="icon">⏻</span> Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <main class="main-content">
        {{-- 1. Affichage des erreurs de validation (Global) --}}
        @if ($errors->any())
            <div class="card"
                style="background: #fff5f5; color: var(--red); padding: 15px; border-radius: var(--radius-md); margin-bottom: 20px; border: 1px solid var(--red);">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 2. Affichage du message de succès (Flash) --}}
        @if (session('success'))
            <div id="flash-message" class="card"
                style="background: #f0fff4; color: var(--primary-green); border: 1px solid #c6f6d5; margin-bottom: 20px; padding: 15px;">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>

</html>

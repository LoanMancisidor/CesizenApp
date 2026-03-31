<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CESIZen - Admin</title>
    
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <header class="header-banner">
        <a href="{{ route('home') }}" class="brand-link">
            <h1>CESIZen Admin</h1>
        </a>

        <nav class="nav-menu">
            <a href="{{ route('articles.index') }}" class="nav-link {{ request()->is('admin/articles*') ? 'active' : '' }}">Articles</a>
            <a href="#" class="nav-link">Émotions</a>
            <a href="#" class="nav-link">Utilisateurs</a>
            
            <form method="POST" action="{{ route('logout') }}" class="nav-right">
                @csrf
                <button type="submit" class="nav-link logout-link">
                    Déconnexion
                </button>
            </form>
        </nav>
    </header>

    <main class="main-content">
        @yield('content')
    </main>

</body>
</html>
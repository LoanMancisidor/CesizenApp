<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | CESIZen</title>
    @vite(['resources/css/app.css'])
</head>

<body class="auth-page">

    <div class="form-container card">
        <div style="text-align: center; margin-bottom: 2rem;">
            <a href="/">
                <img src="{{ asset('images/logo.png') }}" alt="Logo CESIZen" style="height: 80px; width: auto;">
            </a>
            <h2 style="margin-top: 1rem;">Espace Admin</h2>
            <p style="color: #718096;">Connectez-vous pour gérer la plateforme</p>
        </div>

        {{-- Affichage des erreurs Laravel --}}
        @if ($errors->any())
            <div style="color: var(--red); margin-bottom: 1rem; font-size: 0.9rem;">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label for="password">Mot de passe</label>
                <input id="password" type="password" name="password" required>
            </div>

            <button type="submit" class="btn-submit">
                Se connecter
            </button>
        </form>

        <p style="text-align: center; margin-top: 1.5rem; font-size: 0.85rem; font-weight: 700; color: var(--red);">Build : avant démo</p>
    </div>

</body>

</html>

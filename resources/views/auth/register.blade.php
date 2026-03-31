<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription | CESIZen</title>
    @vite(['resources/css/app.css'])
</head>
<body class="auth-page">

    <div class="form-container card">
        <div style="text-align: center; margin-bottom: 2rem;">
            <a href="/">
                <img src="{{ asset('images/logo.png') }}" alt="Logo CESIZen" style="height: 80px; width: auto;">
            </a>
            <h2 style="margin-top: 1rem;">Créer un compte</h2>
            <p style="color: #718096;">Rejoignez l'administration de CESIZen</p>
        </div>

        {{-- Affichage des erreurs Laravel --}}
        @if ($errors->any())
            <div style="color: var(--red); margin-bottom: 1rem; font-size: 0.9rem;">
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name">Nom complet</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label for="password">Mot de passe</label>
                <input id="password" type="password" name="password" required autocomplete="new-password">
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn-submit">
                S'inscrire
            </button>

            <div style="text-align: center; margin-top: 1.5rem; font-size: 0.85rem;">
                <a href="{{ route('login') }}" style="color: var(--primary-green); font-weight: bold; text-decoration: underline;">
                    Déjà inscrit ? Se connecter
                </a>
            </div>
        </form>
    </div>

</body>
</html>
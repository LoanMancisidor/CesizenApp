# CESIZen

Application de gestion du bien-être et des émotions, composée d'une interface web d'administration (Laravel) et d'une application mobile (Flutter).

---

## Prérequis

### Application web (`cesizen-web`)
- [WAMP](https://www.wampserver.com/) (ou tout serveur Apache/MySQL équivalent)
- PHP >= 8.4
- Composer
- Node.js >= 18 + npm
- MySQL

### Application mobile (`cesizen-mobile`)
- [Flutter SDK](https://docs.flutter.dev/get-started/install) >= 3.11
- Android Studio ou VS Code avec l'extension Flutter
- Un émulateur Android ou un appareil physique

---

## Installation — Application web

### 1. Cloner le projet et se placer dans le dossier web

```bash
cd cesizen-web
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configurer l'environnement

Copier le fichier d'exemple et l'éditer :

```bash
cp .env.example .env
```

Ouvrir `.env` et renseigner les informations de base de données :

```env
DB_DATABASE=cesizenapp
DB_USERNAME=root
DB_PASSWORD=          # laisser vide si pas de mot de passe (WAMP par défaut)
```

### 4. Générer la clé d'application

```bash
php artisan key:generate
```

### 5. Créer la base de données

Dans phpMyAdmin (ou votre client MySQL), créer une base de données vide nommée `cesizenapp`.

### 6. Lancer les migrations et les seeders

```bash
php artisan migrate --seed
```

Cela crée toutes les tables et insère les données de départ :

| Compte          | Email                | Mot de passe  | Rôle          |
|-----------------|----------------------|---------------|---------------|
| Administrateur  | admin@cesizen.fr     | Admin1234!    | Administrateur|
| Utilisateur     | user@cesizen.fr      | User1234!     | Utilisateur   |

### 7. Installer les dépendances front-end

```bash
npm install
```

### 8. Lancer l'application

**En développement** (serveur PHP + Vite en hot-reload) :

```bash
composer run dev
```

L'application est accessible sur : **http://127.0.0.1:8000**

**En production** (build des assets puis serveur PHP) :

```bash
npm run build
php artisan serve
```

---

## Commandes utiles — Application web

| Commande | Description |
|---|---|
| `composer run dev` | Démarre le serveur + Vite en développement |
| `php artisan serve` | Démarre uniquement le serveur PHP |
| `php artisan migrate` | Applique les migrations |
| `php artisan migrate:fresh --seed` | Recrée toutes les tables et re-seed |
| `php artisan db:seed` | Relance uniquement les seeders |
| `php artisan test` | Lance tous les tests |
| `npm run test:cov` | Lance les tests avec rapport de couverture HTML (nécessite Xdebug) |
| `npm run build` | Compile les assets pour la production |

---

## Installation — Application mobile

### 1. Se placer dans le dossier mobile

```bash
cd cesizen-mobile
```

### 2. Installer les dépendances Flutter

```bash
flutter pub get
```

### 3. Configurer l'URL de l'API

L'application mobile se connecte au back-end Laravel. L'URL est définie dans les fichiers du dossier `lib/repositories/`.

- **Émulateur Android** : l'adresse `10.0.2.2` pointe vers le `localhost` de la machine hôte (déjà configuré par défaut)
- **Appareil physique** : remplacer `10.0.2.2` par l'adresse IP locale de votre machine (ex : `192.168.1.XX`), vérifiable avec `ipconfig` sur Windows

> Le back-end Laravel doit être démarré (`php artisan serve`) pour que l'application mobile fonctionne.

### 4. Lancer l'application

```bash
flutter run
```

Sélectionner l'émulateur ou l'appareil cible si plusieurs sont disponibles.

---

## Structure du projet

```
CesizenApp/
├── cesizen-web/        # Application web Laravel (back-end + interface admin)
│   ├── app/
│   │   ├── Http/Controllers/   # Contrôleurs web et API
│   │   ├── Models/             # Modèles Eloquent
│   │   └── Repositories/       # Couche d'accès aux données
│   ├── database/
│   │   ├── migrations/         # Structure de la base de données
│   │   └── seeders/            # Données initiales
│   ├── resources/views/        # Templates Blade
│   ├── routes/
│   │   ├── web.php             # Routes web (interface admin)
│   │   └── api.php             # Routes API (application mobile)
│   └── tests/                  # Tests unitaires et fonctionnels
│
└── cesizen-mobile/     # Application mobile Flutter
    └── lib/
        ├── repositories/       # Appels API
        └── screens/            # Écrans de l'application
```

---

## API — Points d'entrée principaux

| Méthode | Route | Auth | Description |
|---|---|---|---|
| POST | `/api/login` | Non | Connexion (retourne un token) |
| POST | `/api/register` | Non | Inscription |
| POST | `/api/logout` | Oui | Déconnexion |
| PUT | `/api/user/password` | Oui | Changement de mot de passe |
| GET | `/api/articles` | Non | Liste des articles |
| GET | `/api/emotions` | Oui | Liste des émotions |
| POST | `/api/user-emotions` | Oui | Enregistrer une émotion |
| GET | `/api/user-emotions/history` | Oui | Historique personnel |
| GET | `/api/user-emotions/stats` | Oui | Statistiques par émotion |

> Les routes protégées nécessitent le header `Authorization: Bearer {token}`.

---

## Tests

```bash
cd cesizen-web

# Lancer tous les tests
php artisan test

# Lancer uniquement les tests unitaires
php artisan test --testsuite=Unit

# Lancer uniquement les tests fonctionnels
php artisan test --testsuite=Feature

# Rapport de couverture HTML (nécessite Xdebug installé)
npm run test:cov
# Le rapport est généré dans cesizen-web/coverage/index.html
```

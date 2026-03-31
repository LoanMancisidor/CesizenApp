<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::all(); // On récupère tous les articles
        return view('articles.index', compact('articles')); // On les envoie à la vue
    }

    public function create()
    {
        return view('articles.create'); // Affiche ton formulaire
    }

    public function store(Request $request) {
    $request->validate([
        'titre' => 'required',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validation sécu
    ]);

    $path = null;
    if ($request->hasFile('image')) {
        // Stocke l'image dans storage/app/public/articles
        $path = $request->file('image')->store('articles', 'public');
    }

    Article::create([
        'titre' => $request->titre,
        'contenu' => $request->contenu,
        'image_url' => $path,
        'user_id' => 1
    ]);
    
    return redirect()->route('articles.index');
}

    public function edit(string $id)
    {
        $article = Article::findOrFail($id); // Trouve l'article ou renvoie une erreur 404
        return view('articles.edit', compact('article'));
    }

    /**
     * Enregistre les modifications
     */
    public function update(Request $request, string $id)
    {
        $article = Article::findOrFail($id);

        $request->validate([
            'titre' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'titre' => $request->titre,
            'contenu' => $request->contenu,
        ];

        if ($request->hasFile('image')) {
            // Optionnel : Supprimer l'ancienne image du serveur ici pour ne pas encombrer Wamp
            $data['image_url'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($data);

        return redirect()->route('articles.index')->with('success', 'Article mis à jour !');
    }

    /**
     * Supprime l'article
     */
    public function destroy(string $id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        // Si la requête est AJAX (Fetch), on renvoie du JSON
        if (request()->wantsJson()) {
            return response()->json(['success' => 'Article supprimé']);
        }

        // Backup pour le mode classique
        return redirect()->route('articles.index');
    }
}

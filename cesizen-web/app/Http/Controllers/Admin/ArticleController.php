<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ArticleRepository;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function index()
    {
        $articles = $this->articleRepository->getAll();
        return view('articles.index', compact('articles'));
    }

    public function create()
    {
        return view('articles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only('titre', 'contenu');
        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('articles', 'public');
        }

        $this->articleRepository->create($data);

        return redirect()->route('articles.index')->with('success', 'Article créé !');
    }

    public function edit(string $id)
    {
        $article = $this->articleRepository->findById($id);
        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, string $id)
    {
        $article = $this->articleRepository->findById($id);

        $request->validate([
            'titre' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only('titre', 'contenu');

        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('articles', 'public');
        }

        $this->articleRepository->update($article, $data);

        return redirect()->route('articles.index')->with('success', 'Article mis à jour !');
    }

    public function destroy(string $id)
    {
        $article = $this->articleRepository->findById($id);
        $this->articleRepository->delete($article);

        if (request()->wantsJson()) {
            return response()->json(['success' => 'Article supprimé']);
        }

        return redirect()->route('articles.index')->with('success', 'Article supprimé !');
    }
}

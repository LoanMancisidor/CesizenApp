<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Support\Facades\Storage;

class ArticleRepository
{
    public function getAll()
    {
        return Article::all();
    }

    public function findById(string $id)
    {
        return Article::findOrFail($id);
    }

    public function create(array $data)
    {
        return Article::create($data);
    }

    public function update(Article $article, array $data)
    {
        return $article->update($data);
    }

    public function delete(Article $article)
    {
        if ($article->image_url) {
            Storage::disk('public')->delete($article->image_url);
        }
        return $article->delete();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    public function index()
    {
        return view('articles.index', [
            'articles' => Article::published()->with('category', 'tags')->latest('published_at')->paginate(9),
        ]);
    }

    public function show(Article $article)
    {
        abort_unless($article->is_published && $article->published_at && $article->published_at->lte(now()), Response::HTTP_NOT_FOUND);

        return view('articles.show', compact('article'));
    }
}

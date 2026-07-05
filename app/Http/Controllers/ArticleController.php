<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));

        $query = Article::published()
            ->with('category', 'tags')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%")
                        ->orWhere('body', 'like', "%{$search}%");
                });
            })
            ->when($category !== '', function ($query) use ($category) {
                $query->whereHas('category', function ($query) use ($category) {
                    $query->where('slug', $category)->orWhere('name', $category);
                });
            })
            ->latest('published_at');

        $articles = $query->paginate(9)->withQueryString();

        return view('articles.index', [
            'articles' => $articles,
            'categories' => Category::whereHas('articles', fn ($query) => $query->published())->orderBy('name')->get(),
            'search' => $search,
            'selectedCategory' => $category,
            'resultCount' => $articles->total(),
        ]);
    }

    public function show(Article $article)
    {
        abort_unless($article->is_published && $article->published_at && $article->published_at->lte(now()), Response::HTTP_NOT_FOUND);

        $article->load('category', 'tags');

        $relatedArticles = Article::published()
            ->with('category')
            ->whereKeyNot($article->id)
            ->when($article->category_id, fn ($query) => $query->where('category_id', $article->category_id))
            ->latest('published_at')
            ->take(3)
            ->get();

        if ($relatedArticles->count() < 3) {
            $fallback = Article::published()
                ->with('category')
                ->whereKeyNot($article->id)
                ->whereNotIn('id', $relatedArticles->pluck('id'))
                ->latest('published_at')
                ->take(3 - $relatedArticles->count())
                ->get();

            $relatedArticles = $relatedArticles->concat($fallback);
        }

        return view('articles.show', compact('article', 'relatedArticles'));
    }
}

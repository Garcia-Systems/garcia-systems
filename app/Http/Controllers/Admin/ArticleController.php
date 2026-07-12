<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Support\SubstackMetadataFetcher;
use App\Support\SubstackUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $status = $request->query('status') === 'archived' ? 'archived' : 'active';

        $articles = Article::with('category', 'tags')
            ->when($status === 'archived', fn ($query) => $query->onlyTrashed())
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%")
                        ->orWhere('body', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.articles.index', ['articles' => $articles, 'search' => $search, 'status' => $status]);
    }

    public function create(): View
    {
        return view('admin.articles.create', $this->formData(new Article(['is_published' => false])));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->slug($data['slug'] ?? null, $data['title']);
        $data['is_published'] = $request->boolean('is_published');
        $data = $this->prepareExternalArticleData($data);
        $data['published_at'] = $data['is_published'] ? now() : null;
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['tag_ids']);
        $article = Article::create($data);
        $article->tags()->sync($tagIds);
        return redirect()->route('admin.articles.edit', $article)->with('status', 'Article created.');
    }

    public function edit(Article $article): View
    {
        $article->load('tags');
        return view('admin.articles.edit', $this->formData($article));
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $data = $this->validated($request, $article);
        $data['slug'] = $this->slug($data['slug'] ?? null, $data['title']);
        $data['is_published'] = $request->boolean('is_published');
        $data = $this->prepareExternalArticleData($data, $article);
        $data['published_at'] = $data['is_published'] ? ($article->published_at ?? now()) : null;
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['tag_ids']);
        $article->update($data);
        $article->tags()->sync($tagIds);
        return back()->with('status', 'Article updated.');
    }

    public function togglePublish(Article $article): RedirectResponse
    {
        $article->update(['is_published' => ! $article->is_published, 'published_at' => $article->is_published ? null : now()]);
        return back()->with('status', 'Article publication status updated.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('admin.articles.index')->with('status', 'Article archived.');
    }

    public function restore(int $article): RedirectResponse
    {
        $article = Article::onlyTrashed()->findOrFail($article);
        $article->restore();

        return redirect()->route('admin.articles.edit', $article)->with('status', 'Article restored.');
    }

    private function validated(Request $request, ?Article $article = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('articles', 'slug')->ignore($article)],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['exists:tags,id'],
            'featured_image_url' => ['nullable', 'url', 'max:2048'],
            'excerpt' => ['required', 'string', 'max:1000'],
            'body' => ['nullable', 'string'],
            'external_url' => ['nullable', 'url', 'max:2048', function ($attribute, $value, $fail) { if (filled($value) && ! SubstackUrl::isAllowed($value)) $fail('Enter an HTTPS Substack URL.'); }],
            'is_published' => ['nullable', 'boolean'],
        ]);
    }

    private function prepareExternalArticleData(array $data, ?Article $article = null): array
    {
        $data['external_url'] = blank($data['external_url'] ?? null) ? null : trim($data['external_url']);
        $data['substack_embed_code'] = $article?->substack_embed_code;

        if (! $data['external_url']) {
            $data['external_preview_image_url'] = null;
            return $data;
        }

        $metadata = app(SubstackMetadataFetcher::class)->fetch($data['external_url']);
        $data['external_preview_image_url'] = $metadata['image'] ?? $article?->external_preview_image_url;

        return $data;
    }

    private function formData(Article $article): array
    {
        return ['article' => $article, 'categories' => Category::orderBy('name')->get(), 'tags' => Tag::orderBy('name')->get()];
    }

    private function slug(?string $slug, string $title): string
    {
        return Str::slug($slug ?: $title);
    }
}

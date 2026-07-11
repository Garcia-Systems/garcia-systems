<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Video;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $status = $request->query('status') === 'archived' ? 'archived' : 'active';

        $videos = Video::with('article')
            ->when($status === 'archived', fn ($query) => $query->onlyTrashed())
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('transcript', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.videos.index', ['videos' => $videos, 'search' => $search, 'status' => $status]);
    }
    public function create(): View { return view('admin.videos.create', $this->formData(new Video(['is_published' => false]))); }
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['slug'] ?: $data['title']);
        $data['url'] = $data['youtube_url']; unset($data['youtube_url']);
        $data['is_published'] = $request->boolean('is_published');
        $video = Video::create($data);
        return redirect()->route('admin.videos.edit', $video)->with('status', 'Video created.');
    }
    public function edit(Video $video): View { return view('admin.videos.edit', $this->formData($video)); }
    public function update(Request $request, Video $video): RedirectResponse
    {
        $data = $this->validated($request, $video);
        $data['slug'] = Str::slug($data['slug'] ?: $data['title']);
        $data['url'] = $data['youtube_url']; unset($data['youtube_url']);
        $data['is_published'] = $request->boolean('is_published');
        $video->update($data);
        return back()->with('status', 'Video updated.');
    }
    public function togglePublish(Video $video): RedirectResponse
    {
        $video->update(['is_published' => ! $video->is_published]);
        return back()->with('status', 'Video publication status updated.');
    }

    public function destroy(Video $video): RedirectResponse
    {
        $video->delete();

        return redirect()->route('admin.videos.index')->with('status', 'Video archived.');
    }

    public function restore(int $video): RedirectResponse
    {
        $video = Video::onlyTrashed()->findOrFail($video);
        $video->restore();

        return redirect()->route('admin.videos.edit', $video)->with('status', 'Video restored.');
    }
    private function validated(Request $request, ?Video $video = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'], 'slug' => ['nullable', 'string', 'max:255', Rule::unique('videos', 'slug')->ignore($video)],
            'youtube_url' => ['required', 'url', 'max:2048', function ($attribute, $value, $fail) { if (! Video::parseYoutubeVideoId($value)) $fail('Enter a valid YouTube video URL.'); }], 'description' => ['required', 'string'], 'transcript' => ['nullable', 'string'],
            'article_id' => ['nullable', 'exists:articles,id'], 'is_published' => ['nullable', 'boolean'],
        ]);
    }
    private function formData(Video $video): array { return ['video' => $video, 'articles' => Article::orderBy('title')->get()]; }
}

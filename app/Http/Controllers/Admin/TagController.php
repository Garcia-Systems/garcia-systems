<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        return view('admin.tags.index', ['tags' => Tag::withCount('articles')->orderBy('name')->paginate(50)]);
    }

    public function store(Request $request): RedirectResponse
    {
        Tag::create($this->validated($request));

        return back()->with('status', 'Tag created.');
    }

    public function edit(Tag $tag): View
    {
        return view('admin.tags.edit', ['tag' => $tag->loadCount('articles')]);
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $tag->update($this->validated($request, $tag));

        return redirect()->route('admin.tags.index')->with('status', 'Tag updated.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        if ($tag->articles()->exists()) {
            return back()->with('status', 'Tag cannot be deleted while related articles exist.');
        }

        $tag->delete();

        return redirect()->route('admin.tags.index')->with('status', 'Tag deleted.');
    }

    private function validated(Request $request, ?Tag $tag = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('tags', 'slug')->ignore($tag)],
        ]);

        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);

        return $data;
    }
}

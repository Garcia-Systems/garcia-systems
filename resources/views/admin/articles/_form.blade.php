@csrf
@php($inputClass = 'admin-control w-full')
<div class="grid gap-4">
    <label>Title<input class="{{ $inputClass }}" name="title" value="{{ old('title',$article->title) }}" required></label>
    <label>Slug<input class="{{ $inputClass }}" name="slug" value="{{ old('slug',$article->slug) }}"></label>
    <label>SEO title<input class="{{ $inputClass }}" name="seo_title" value="{{ old('seo_title',$article->seo_title) }}"></label>
    <label>SEO description<textarea class="{{ $inputClass }}" name="seo_description">{{ old('seo_description',$article->seo_description) }}</textarea></label>
    <label>Category<select class="{{ $inputClass }}" name="category_id"><option value="">None</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id',$article->category_id)==$category->id)>{{ $category->name }}</option>@endforeach</select></label>
    <fieldset><legend>Tags</legend><div class="flex flex-wrap gap-3">@foreach($tags as $tag)<label><input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" @checked(in_array($tag->id, old('tag_ids',$article->tags->pluck('id')->all() ?? [])))> {{ $tag->name }}</label>@endforeach</div></fieldset>
    <label>Featured image URL<input class="{{ $inputClass }}" name="featured_image_url" value="{{ old('featured_image_url',$article->featured_image_url) }}"></label>
    <label>Excerpt<textarea class="{{ $inputClass }}" name="excerpt" required>{{ old('excerpt',$article->excerpt) }}</textarea></label>
    <label>Body<textarea class="{{ $inputClass }} min-h-64" name="body">{{ old('body',$article->body) }}</textarea><span class="mt-1 block text-sm text-slate-300">Optional for external Substack article previews.</span></label>
    <label>Substack article URL (optional)<input class="{{ $inputClass }}" name="external_url" value="{{ old('external_url',$article->external_url) }}" placeholder="https://garciasystems.substack.com/p/example"><span class="mt-1 block text-sm text-slate-300">Paste an HTTPS Substack article URL. Garcia Systems will show a preview card instead of an embed.</span></label>
    <label><input type="checkbox" name="is_published" value="1" @checked(old('is_published',$article->is_published))> Published</label>
    <button class="rounded bg-cyan-400 px-4 py-2 font-bold text-slate-950">Save article</button>
</div>

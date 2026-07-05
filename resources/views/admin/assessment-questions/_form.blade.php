@csrf
<div class="grid gap-4">
    <label>Question text<textarea class="w-full text-slate-900" name="question" required>{{ old('question', $question->question) }}</textarea></label>
    <label>Help text<textarea class="w-full text-slate-900" name="help_text">{{ old('help_text', $question->help_text) }}</textarea></label>
    <label>Category<input class="w-full text-slate-900" name="category" value="{{ old('category', $question->category) }}"></label>
    <label>Order<input class="w-full text-slate-900" name="sort_order" type="number" min="0" value="{{ old('sort_order', $question->sort_order) }}" required></label>
    <label>Weight<input class="w-full text-slate-900" name="weight" type="number" min="0" step="0.01" value="{{ old('weight', $question->weight ?? 1) }}" required></label>
    <label>Status<select class="w-full text-slate-900" name="is_active" required><option value="1" @selected(old('is_active', $question->is_active) == 1)>Active</option><option value="0" @selected(old('is_active', $question->is_active) == 0)>Inactive</option></select></label>
    <button class="rounded bg-cyan-400 px-4 py-2 font-bold text-slate-950">Save question</button>
</div>

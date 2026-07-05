<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentQuestion;
use Illuminate\Http\Request;

class AssessmentQuestionController extends Controller
{
    public function index()
    {
        return view('admin.assessment-questions.index', [
            'questions' => AssessmentQuestion::query()->orderBy('sort_order')->orderBy('id')->paginate(50),
        ]);
    }

    public function create()
    {
        return view('admin.assessment-questions.create', [
            'question' => new AssessmentQuestion(['sort_order' => 0, 'weight' => 1, 'is_active' => true]),
        ]);
    }

    public function store(Request $request)
    {
        $question = AssessmentQuestion::create($this->validated($request));

        return redirect()->route('admin.assessment-questions.edit', $question)->with('status', 'Question created.');
    }

    public function edit(AssessmentQuestion $assessmentQuestion)
    {
        return view('admin.assessment-questions.edit', ['question' => $assessmentQuestion]);
    }

    public function update(Request $request, AssessmentQuestion $assessmentQuestion)
    {
        $assessmentQuestion->update($this->validated($request));

        return redirect()
            ->route('admin.assessment-questions.edit', $assessmentQuestion->getKey())
            ->with('status', 'Question updated.');
    }

    public function destroy(AssessmentQuestion $assessmentQuestion)
    {
        if ($assessmentQuestion->responses()->exists()) {
            $assessmentQuestion->update(['is_active' => false]);

            return redirect()->route('admin.assessment-questions.index')->with('status', 'Question has submissions, so it was deactivated.');
        }

        $assessmentQuestion->delete();

        return redirect()->route('admin.assessment-questions.index')->with('status', 'Question deleted.');
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($data['orders'] as $id => $order) {
            AssessmentQuestion::whereKey($id)->update(['sort_order' => $order]);
        }

        return redirect()->route('admin.assessment-questions.index')->with('status', 'Question order updated.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'help_text' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}

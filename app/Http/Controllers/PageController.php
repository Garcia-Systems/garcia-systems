<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResponse;
use App\Models\ContactSubmission;
use App\Models\FrictionPoint;
use App\Models\Industry;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home', [
            'articles' => Article::published()->latest('published_at')->take(3)->get(),
            'videos' => Video::published()->latest()->take(3)->get(),
            'frictions' => FrictionPoint::with('workflow.industry')->take(3)->get(),
        ]);
    }

    public function about()
    {
        return view('pages.simple', [
            'title' => 'About Garcia Systems',
            'body' => 'Garcia Systems helps growing teams identify practical automation opportunities, clarify workflows, and ship useful technology in measured phases.',
        ]);
    }

    public function services() { return view('pages.services'); }
    public function videos() { return view('pages.videos', ['videos' => Video::published()->latest()->get()]); }
    public function tools() { return view('pages.tools'); }
    public function atlas() { return view('pages.atlas', ['industries' => Industry::with('workflows.frictionPoints.solutionPatterns')->get()]); }
    public function contact() { return view('pages.contact'); }

    public function submitContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'company' => ['nullable', 'string', 'max:180'],
            'service_interest' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('contact')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        ContactSubmission::create($data);

        return back()->with('status', 'Thanks — your message has been saved.');
    }

    public function assessment()
    {
        return view('pages.assessment', ['questions' => AssessmentQuestion::orderBy('sort_order')->get()]);
    }

    public function submitAssessment(Request $request)
    {
        $questionIds = AssessmentQuestion::query()->pluck('id')->map(fn ($id) => (string) $id)->all();

        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:180'],
            'company' => ['nullable', 'string', 'max:180'],
            'responses' => ['required', 'array', 'size:'.count($questionIds)],
            'responses.*' => ['required', 'integer', Rule::in([1, 2, 3, 4, 5])],
        ]);

        $validator->after(function ($validator) use ($request, $questionIds) {
            $responses = $request->input('responses', []);

            if (! is_array($responses)) {
                return;
            }

            $submittedIds = array_map('strval', array_keys($responses));
            sort($submittedIds);
            sort($questionIds);

            if ($submittedIds !== $questionIds) {
                $validator->errors()->add('responses', 'Please answer each current assessment question once.');
            }
        });

        $data = $validator->validate();
        $score = $this->calculateAssessmentScore($data['responses']);
        [$tier, $summary] = $this->assessmentResultForScore($score);

        $assessment = Assessment::create([
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'company' => $data['company'] ?? null,
            'score' => $score,
            'result_tier' => $tier,
            'summary' => $summary,
        ]);

        foreach ($data['responses'] as $qid => $value) {
            AssessmentResponse::create([
                'assessment_id' => $assessment->id,
                'assessment_question_id' => $qid,
                'score' => (int) $value,
            ]);
        }

        return redirect()->route('assessment.result', $assessment);
    }

    public function assessmentResult(Assessment $assessment)
    {
        return view('pages.assessment-result', ['assessment' => $assessment]);
    }

    private function calculateAssessmentScore(array $responses): int
    {
        return collect($responses)->map(fn ($value) => (int) $value)->sum();
    }

    private function assessmentResultForScore(int $score): array
    {
        return match (true) {
            $score >= 16 => ['Ready to prioritize pilots', 'You appear ready to select a focused pilot and define success metrics.'],
            $score >= 10 => ['Foundation in progress', 'You have useful foundations; start with one workflow and tighten data/process ownership.'],
            default => ['Early readiness', 'Begin with workflow clarity, data quality, and a narrow business problem before investing heavily.'],
        };
    }
}

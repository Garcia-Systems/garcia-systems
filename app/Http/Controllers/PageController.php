<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResponse;
use App\Models\Capability;
use App\Models\CompanyType;
use App\Models\ContactSubmission;
use App\Models\Department;
use App\Models\FrictionPoint;
use App\Models\Industry;
use App\Models\SolutionPattern;
use App\Models\Video;
use App\Models\Workflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function atlas(Request $request)
    {
        $filters = collect($request->only([
            'industry',
            'company_type',
            'department',
            'workflow',
            'friction_point',
            'capability',
            'solution_pattern',
        ]))->filter(fn ($value) => filled($value))->map(fn ($value) => str($value)->slug()->toString())->all();

        $workflows = Workflow::query()
            ->with([
                'industry',
                'companyType',
                'department',
                'frictionPoints' => function ($query) use ($filters) {
                    $query
                        ->when($filters['friction_point'] ?? null, fn ($query, $slug) => $query->where('slug', $slug))
                        ->when($filters['solution_pattern'] ?? null, fn ($query, $slug) => $query->whereHas('solutionPatterns', fn ($query) => $query->where('slug', $slug)))
                        ->when($filters['capability'] ?? null, fn ($query, $slug) => $query->whereHas('solutionPatterns.capabilities', fn ($query) => $query->where('slug', $slug)));
                },
                'frictionPoints.solutionPatterns' => function ($query) use ($filters) {
                    $query
                        ->when($filters['solution_pattern'] ?? null, fn ($query, $slug) => $query->where('slug', $slug))
                        ->when($filters['capability'] ?? null, fn ($query, $slug) => $query->whereHas('capabilities', fn ($query) => $query->where('slug', $slug)));
                },
                'frictionPoints.solutionPatterns.capabilities' => function ($query) use ($filters) {
                    $query->when($filters['capability'] ?? null, fn ($query, $slug) => $query->where('slug', $slug));
                },
            ])
            ->when($filters['industry'] ?? null, fn ($query, $slug) => $query->whereHas('industry', fn ($query) => $query->where('slug', $slug)))
            ->when($filters['company_type'] ?? null, fn ($query, $slug) => $query->whereHas('companyType', fn ($query) => $query->where('slug', $slug)))
            ->when($filters['department'] ?? null, fn ($query, $slug) => $query->whereHas('department', fn ($query) => $query->where('slug', $slug)))
            ->when($filters['workflow'] ?? null, fn ($query, $slug) => $query->where('slug', $slug))
            ->when($filters['friction_point'] ?? null, fn ($query, $slug) => $query->whereHas('frictionPoints', fn ($query) => $query->where('slug', $slug)))
            ->when($filters['solution_pattern'] ?? null, fn ($query, $slug) => $query->whereHas('frictionPoints.solutionPatterns', fn ($query) => $query->where('slug', $slug)))
            ->when($filters['capability'] ?? null, fn ($query, $slug) => $query->whereHas('frictionPoints.solutionPatterns.capabilities', fn ($query) => $query->where('slug', $slug)))
            ->orderBy('name')
            ->get();

        $articles = Article::published()->latest('published_at')->get();
        $videos = Video::published()->latest()->get();
        $services = collect([
            'Product Discovery',
            'Solutions Engineering',
            'Workflow Modernization',
            'Technical Liaison Services',
            'AI Opportunity Assessment',
            'Product Execution Support',
        ]);

        return view('pages.atlas', [
            'workflows' => $workflows,
            'filters' => $filters,
            'filterOptions' => [
                'industry' => Industry::orderBy('name')->get(),
                'company_type' => CompanyType::orderBy('name')->get(),
                'department' => Department::orderBy('name')->get(),
                'workflow' => Workflow::orderBy('name')->get(),
                'friction_point' => FrictionPoint::orderBy('name')->get(),
                'capability' => Capability::orderBy('name')->get(),
                'solution_pattern' => SolutionPattern::orderBy('name')->get(),
            ],
            'articles' => $articles,
            'videos' => $videos,
            'services' => $services,
            'summary' => [
                'workflows' => $workflows->count(),
                'friction_points' => $workflows->pluck('frictionPoints')->flatten()->count(),
                'solution_patterns' => $workflows->pluck('frictionPoints')->flatten()->pluck('solutionPatterns')->flatten()->unique('id')->count(),
                'capabilities' => $workflows->pluck('frictionPoints')->flatten()->pluck('solutionPatterns')->flatten()->pluck('capabilities')->flatten()->unique('id')->count(),
                'articles' => $articles->count(),
                'videos' => $videos->count(),
                'services' => $services->count(),
            ],
        ]);
    }
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
                ->with('contact_error_summary', 'Please fix the highlighted fields and try again.')
                ->withInput();
        }

        $data = $validator->validated();

        ContactSubmission::create($data);

        return back()->with('status', 'Thanks — your message has been saved.');
    }

    public function assessment()
    {
        return view('pages.assessment', [
            'questions' => AssessmentQuestion::query()
                ->active()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function submitAssessment(Request $request)
    {
        $questions = AssessmentQuestion::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        $questionIds = $questions->pluck('id')->map(fn ($id) => (string) $id)->all();

        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:180'],
            'company' => ['nullable', 'string', 'max:180'],
            'responses' => ['required', 'array'],
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
        $score = $this->calculateAssessmentScore($data['responses'], $questions->keyBy('id'));
        [$tier, $summary] = $this->assessmentResultForScore($score);

        $assessment = DB::transaction(function () use ($data, $score, $tier, $summary) {
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

            return $assessment;
        });

        return redirect()->route('assessment.result', $assessment);
    }

    public function assessmentResult(Assessment $assessment)
    {
        return view('pages.assessment-result', ['assessment' => $assessment]);
    }

    private function calculateAssessmentScore(array $responses, $questions): int
    {
        return (int) round(collect($responses)->sum(function ($value, $questionId) use ($questions) {
            $weight = (float) ($questions->get((int) $questionId)?->weight ?? 1);

            return (int) $value * $weight;
        }));
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

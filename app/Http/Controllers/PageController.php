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
use App\Models\Lead;
use App\Models\SolutionPattern;
use App\Models\Video;
use App\Models\Workflow;
use App\Notifications\AssessmentSubmitted;
use App\Notifications\ContactSubmissionReceived;
use App\Notifications\LeadSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
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
        return view('pages.about');
    }

    public function services() { return view('pages.services'); }
    public function videos() { return view('pages.videos', ['videos' => Video::published()->latest()->get()]); }
    public function tools() { return view('pages.tools'); }

    public function atlas(Request $request)
    {
        abort_unless(config('garcia.features.opportunity_atlas'), 404);
        $filters = collect($request->only([
            'industry',
            'company_type',
            'department',
            'workflow',
            'friction_point',
            'capability',
            'solution_pattern',
        ]))->filter(fn ($value) => filled($value))->map(fn ($value) => str($value)->slug()->toString())->all();
        $keyword = str($request->query('q', ''))->trim()->toString();

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
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query
                        ->where('name', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhereHas('industry', fn ($query) => $query->where('name', 'like', "%{$keyword}%"))
                        ->orWhereHas('companyType', fn ($query) => $query->where('name', 'like', "%{$keyword}%"))
                        ->orWhereHas('department', fn ($query) => $query->where('name', 'like', "%{$keyword}%"))
                        ->orWhereHas('frictionPoints', fn ($query) => $query->where('name', 'like', "%{$keyword}%")->orWhere('description', 'like', "%{$keyword}%"))
                        ->orWhereHas('frictionPoints.solutionPatterns', fn ($query) => $query->where('name', 'like', "%{$keyword}%")->orWhere('description', 'like', "%{$keyword}%"))
                        ->orWhereHas('frictionPoints.solutionPatterns.capabilities', fn ($query) => $query->where('name', 'like', "%{$keyword}%")->orWhere('description', 'like', "%{$keyword}%"));
                });
            })
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
            'keyword' => $keyword,
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
            'website' => ['nullable', 'prohibited'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('contact')
                ->withErrors($validator)
                ->with('contact_error_summary', 'Please fix the highlighted fields and try again.')
                ->withInput();
        }

        $data = $validator->validated();

        unset($data['website']);

        $submission = ContactSubmission::create($data);
        $lead = Lead::createOrUpdateFromContactSubmission($submission);

        $this->sendContactMailNotifications($submission, $lead);

        return back()->with('status', 'Thanks — your message has been saved.');
    }

    private function sendContactMailNotifications(ContactSubmission $submission, Lead $lead): void
    {
        $baseContext = [
            'contact_submission_id' => $submission->id,
            'lead_id' => $lead->id,
            'mailer' => config('mail.default'),
            'queue_connection' => config('queue.default'),
        ];

        $internalRecipient = config('mail.lead_notification_email');
        $confirmationRecipient = $submission->email;

        $this->sendContactMailNotification(
            'contact.mail.internal',
            $baseContext + [
                'internal_notification_recipient' => $internalRecipient,
                'visitor_confirmation_recipient' => $confirmationRecipient,
            ],
            fn () => Notification::route('mail', $internalRecipient)
                ->notify(new LeadSubmitted($lead, $submission))
        );

        $this->sendContactMailNotification(
            'contact.mail.confirmation',
            $baseContext + [
                'internal_notification_recipient' => $internalRecipient,
                'visitor_confirmation_recipient' => $confirmationRecipient,
            ],
            fn () => Notification::route('mail', [$confirmationRecipient => $submission->name])
                ->notify(new ContactSubmissionReceived($submission))
        );
    }

    private function sendContactMailNotification(string $eventNamespace, array $context, callable $send): void
    {
        Log::info($eventNamespace.'.start', $context);

        try {
            $send();

            Log::info($eventNamespace.'.sent', $context);
        } catch (\Throwable $exception) {
            Log::error($eventNamespace.'.failed', $context + [
                'exception_class' => $exception::class,
                'exception_message' => $exception->getMessage(),
            ]);
        }
    }

    public function assessment()
    {
        abort_unless(config('garcia.features.ai_assessment'), 404);

        return view('pages.assessment', ['questions' => AssessmentQuestion::where('is_active', true)->orderBy('sort_order')->get()]);
    }

    public function submitAssessment(Request $request)
    {
        abort_unless(config('garcia.features.ai_assessment'), 404);

        $questionIds = AssessmentQuestion::query()->where('is_active', true)->pluck('id')->map(fn ($id) => (string) $id)->all();

        if (count($questionIds) === 0) {
            return redirect()->route('assessment')->with('assessment_unavailable', 'The AI Readiness Assessment is temporarily unavailable while the question set is being reviewed.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:180'],
            'company' => ['nullable', 'string', 'max:180'],
            'responses' => ['required', 'array', 'size:'.count($questionIds)],
            'responses.*' => ['required', 'integer', Rule::in([1, 2, 3, 4, 5])],
            'website' => ['nullable', 'prohibited'],
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
        unset($data['website']);

        $score = $this->calculateAssessmentScore($data['responses']);
        $categoryScores = $this->assessmentCategoryScores($data['responses']);
        $result = $this->assessmentResultForScore($score, $categoryScores);

        $assessment = Assessment::create([
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'company' => $data['company'] ?? null,
            'score' => $score,
            'result_tier' => $result['tier'],
            'summary' => $result['summary'],
            'category_scores' => $categoryScores,
            'risks' => $result['risks'],
            'next_steps' => $result['next_steps'],
            'recommendations' => $result['recommendations'],
            'service_cta' => $result['service_cta'],
        ]);

        $lead = Lead::createOrUpdateFromAssessment($assessment);

        Notification::route('mail', config('mail.lead_notification_email'))
            ->notify(new AssessmentSubmitted($assessment, $lead));

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
        $assessment->loadMissing('responses.question');

        $categoryScores = $assessment->category_scores ?: $this->assessmentCategoryScores(
            $assessment->responses->pluck('score', 'assessment_question_id')->all()
        );
        $result = $this->assessmentResultForScore($assessment->score, $categoryScores);

        return view('pages.assessment-result', [
            'assessment' => $assessment,
            'categoryScores' => $categoryScores,
            'result' => $result,
        ]);
    }

    private function calculateAssessmentScore(array $responses): int
    {
        return collect($responses)->map(fn ($value) => (int) $value)->sum();
    }

    private function assessmentCategoryScores(array $responses): array
    {
        $questions = AssessmentQuestion::query()
            ->whereIn('id', array_keys($responses))
            ->orderBy('sort_order')
            ->get()
            ->keyBy('id');

        $fallbackCategories = [
            1 => 'Workflow documentation',
            2 => 'Data readiness',
            3 => 'Pilot selection',
            4 => 'Stakeholder alignment',
        ];

        return collect($responses)
            ->map(function ($score, $questionId) use ($questions, $fallbackCategories) {
                $question = $questions->get((int) $questionId);
                $sortOrder = $question?->sort_order ?: 0;
                $label = $question?->category ?: ($fallbackCategories[$sortOrder] ?? 'Readiness area');

                return [
                    'question_id' => (int) $questionId,
                    'label' => $label,
                    'score' => (int) $score,
                    'max_score' => 5,
                    'question' => $question?->question,
                ];
            })
            ->sortBy(fn ($category) => $questions->get($category['question_id'])?->sort_order ?? $category['question_id'])
            ->values()
            ->all();
    }

    private function assessmentResultForScore(int $score, array $categoryScores = []): array
    {
        $tier = match (true) {
            $score >= 18 => 'Advanced',
            $score >= 14 => 'Ready',
            $score >= 9 => 'Emerging',
            default => 'Early',
        };

        $tiers = [
            'Early' => [
                'summary' => 'Your team is still forming the operating foundation needed for useful AI or automation work.',
                'risks' => ['Unclear workflows can create automation around the wrong steps.', 'Inconsistent data may limit trustworthy outputs.'],
                'next_steps' => ['Document one priority workflow from trigger to outcome.', 'Identify the data, owner, and decision points that support that workflow.'],
                'service_cta' => 'Start with an AI Opportunity Assessment to clarify the best first use case.',
            ],
            'Emerging' => [
                'summary' => 'You have useful foundations, but a pilot will need tighter scope, ownership, and data checks.',
                'risks' => ['A broad pilot could dilute momentum.', 'Unresolved ownership gaps may slow implementation.'],
                'next_steps' => ['Pick one workflow with visible friction and measurable impact.', 'Confirm stakeholders, data sources, and success metrics before selecting tools.'],
                'service_cta' => 'Use Garcia Systems workflow modernization to turn readiness into a focused pilot plan.',
            ],
            'Ready' => [
                'summary' => 'You appear ready to prioritize a focused pilot with clear success metrics and operating support.',
                'risks' => ['Teams may still overbuild if the pilot is not constrained.', 'Change management can lag behind technical execution.'],
                'next_steps' => ['Define a 30- to 60-day pilot with a narrow workflow and adoption metric.', 'Create a lightweight implementation backlog and measurement plan.'],
                'service_cta' => 'Bring in Garcia Systems product discovery to shape and validate the pilot.',
            ],
            'Advanced' => [
                'summary' => 'Your foundation supports more systematic AI and automation execution across priority workflows.',
                'risks' => ['Multiple opportunities may compete without a clear portfolio view.', 'Governance and measurement need to keep pace with delivery.'],
                'next_steps' => ['Rank opportunities by value, feasibility, and risk.', 'Establish reusable delivery patterns for data, workflow, and stakeholder review.'],
                'service_cta' => 'Partner with Garcia Systems on solutions engineering and product execution support.',
            ],
        ];

        return array_merge(['tier' => $tier], $tiers[$tier], [
            'recommendations' => $this->assessmentRecommendations($categoryScores),
        ]);
    }

    private function assessmentRecommendations(array $categoryScores): array
    {
        $recommendations = [
            'Workflow documentation' => 'Create a simple workflow map that captures triggers, handoffs, decisions, exceptions, and current pain points.',
            'Data readiness' => 'Inventory the data sources used in the workflow and flag missing fields, duplicate entry, quality issues, and ownership gaps.',
            'Pilot selection' => 'Choose one automation candidate with measurable time savings, manageable risk, and a clear before-and-after success metric.',
            'Stakeholder alignment' => 'Confirm the process owner, daily users, approver, and technical contact before investing in implementation work.',
        ];

        $selected = collect($categoryScores)
            ->sortBy('score')
            ->filter(fn ($category) => $category['score'] <= 3)
            ->map(fn ($category) => $recommendations[$category['label']] ?? 'Clarify the business problem, owner, data inputs, and measurable outcome before selecting an AI or automation tool.')
            ->unique()
            ->take(4)
            ->values()
            ->all();

        if (count($selected) >= 2) {
            return $selected;
        }

        return collect($selected)
            ->merge([
                'Review the strongest workflow area and use it as the template for your first pilot project.',
                'Keep the first initiative narrow enough to validate adoption and business value within one operating cycle.',
            ])
            ->unique()
            ->take(4)
            ->values()
            ->all();
    }
}

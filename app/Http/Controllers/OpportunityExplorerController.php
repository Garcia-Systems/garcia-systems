<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Capability;
use App\Models\CompanyType;
use App\Models\Department;
use App\Models\FrictionPoint;
use App\Models\Industry;
use App\Models\Video;
use App\Models\Workflow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OpportunityExplorerController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->only(['industry', 'company_type', 'department', 'workflow', 'friction_point', 'capability', 'search']);

        $capabilities = Capability::query()
            ->with([
                'solutionPatterns.frictionPoints.workflow.industry',
                'solutionPatterns.frictionPoints.workflow.companyType',
                'solutionPatterns.frictionPoints.workflow.department',
            ])
            ->when($filters['capability'] ?? null, fn (Builder $query, string $slug) => $query->where('slug', $slug))
            ->when($filters['industry'] ?? null, fn (Builder $query, string $slug) => $query->whereHas('solutionPatterns.frictionPoints.workflow.industry', fn (Builder $relation) => $relation->where('slug', $slug)))
            ->when($filters['company_type'] ?? null, fn (Builder $query, string $slug) => $query->whereHas('solutionPatterns.frictionPoints.workflow.companyType', fn (Builder $relation) => $relation->where('slug', $slug)))
            ->when($filters['department'] ?? null, fn (Builder $query, string $slug) => $query->whereHas('solutionPatterns.frictionPoints.workflow.department', fn (Builder $relation) => $relation->where('slug', $slug)))
            ->when($filters['workflow'] ?? null, fn (Builder $query, string $slug) => $query->whereHas('solutionPatterns.frictionPoints.workflow', fn (Builder $relation) => $relation->where('slug', $slug)))
            ->when($filters['friction_point'] ?? null, fn (Builder $query, string $slug) => $query->whereHas('solutionPatterns.frictionPoints', fn (Builder $relation) => $relation->where('slug', $slug)))
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhereHas('solutionPatterns.frictionPoints', fn (Builder $relation) => $relation->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('solutionPatterns.frictionPoints.workflow', fn (Builder $relation) => $relation->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('solutionPatterns.frictionPoints.workflow.industry', fn (Builder $relation) => $relation->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('name')
            ->get()
            ->map(function (Capability $capability) use ($filters) {
                $matchedFrictionPoints = $capability->solutionPatterns
                    ->flatMap->frictionPoints
                    ->unique('id')
                    ->filter(fn (FrictionPoint $frictionPoint) => $this->frictionPointMatches($frictionPoint, $filters))
                    ->values();

                $capability->setRelation('matchedFrictionPoints', $matchedFrictionPoints);

                return $capability;
            })
            ->filter(fn (Capability $capability) => $capability->getRelation('matchedFrictionPoints')->isNotEmpty())
            ->values();

        $articles = Article::published()->with('tags')->latest('published_at')->get();
        $videos = Video::published()->latest()->get();

        return view('pages.opportunity-explorer', [
            'capabilities' => $capabilities,
            'industries' => Industry::orderBy('name')->get(),
            'companyTypes' => CompanyType::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'workflows' => Workflow::orderBy('name')->get(),
            'frictionPoints' => FrictionPoint::orderBy('name')->get(),
            'allCapabilities' => Capability::orderBy('name')->get(),
            'articles' => $articles,
            'videos' => $videos,
            'services' => $this->services(),
            'filters' => $filters,
        ]);
    }

    private function frictionPointMatches(FrictionPoint $frictionPoint, array $filters): bool
    {
        $workflow = $frictionPoint->workflow;

        if (($filters['industry'] ?? null) && $workflow?->industry?->slug !== $filters['industry']) {
            return false;
        }

        if (($filters['company_type'] ?? null) && $workflow?->companyType?->slug !== $filters['company_type']) {
            return false;
        }

        if (($filters['department'] ?? null) && $workflow?->department?->slug !== $filters['department']) {
            return false;
        }

        if (($filters['workflow'] ?? null) && $workflow?->slug !== $filters['workflow']) {
            return false;
        }

        if (($filters['friction_point'] ?? null) && $frictionPoint->slug !== $filters['friction_point']) {
            return false;
        }

        if (! ($filters['search'] ?? null)) {
            return true;
        }

        $searchableContext = str(implode(' ', array_filter([
            $frictionPoint->name,
            $frictionPoint->slug,
            $frictionPoint->description,
            $frictionPoint->impact,
            $workflow?->name,
            $workflow?->slug,
            $workflow?->description,
            $workflow?->industry?->name,
            $workflow?->industry?->slug,
            $workflow?->companyType?->name,
            $workflow?->companyType?->slug,
            $workflow?->department?->name,
            $workflow?->department?->slug,
        ])))->lower();

        return $searchableContext->contains(str($filters['search'])->lower()->toString());
    }

    private function services(): array
    {
        return [
            ['title' => 'Product Discovery', 'keywords' => ['intake', 'workflow', 'visibility', 'knowledge'], 'description' => 'Clarify the problem, users, requirements, MVP scope, and decision-ready roadmap.'],
            ['title' => 'Solutions Engineering', 'keywords' => ['integration', 'reporting', 'dashboard', 'records', 'data'], 'description' => 'Shape practical internal tools, integration paths, reporting layers, and technical recommendations.'],
            ['title' => 'Workflow Modernization', 'keywords' => ['automation', 'routing', 'exception', 'approval', 'operations'], 'description' => 'Redesign recurring work so handoffs, ownership, status, and automation foundations are clear.'],
            ['title' => 'Technical Liaison Services', 'keywords' => ['vendor', 'coordination', 'systems'], 'description' => 'Translate business goals into vendor conversations, acceptance criteria, and implementation decisions.'],
            ['title' => 'AI Opportunity Assessment', 'keywords' => ['knowledge', 'document', 'data quality'], 'description' => 'Assess practical AI and automation opportunities against workflow, data, risk, and ownership readiness.'],
            ['title' => 'Product Execution Support', 'keywords' => ['mvp', 'roadmap', 'execution'], 'description' => 'Move validated product and systems initiatives from plan to shipped operational improvement.'],
        ];
    }
}

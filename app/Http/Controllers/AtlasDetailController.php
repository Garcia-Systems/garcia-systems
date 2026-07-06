<?php

namespace App\Http\Controllers;

use App\Models\Capability;
use App\Models\CompanyType;
use App\Models\Department;
use App\Models\FrictionPoint;
use App\Models\Industry;
use App\Models\SolutionPattern;
use App\Models\Workflow;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class AtlasDetailController extends Controller
{
    public function industry(Industry $industry)
    {
        $industry->load('workflows.companyType', 'workflows.department', 'workflows.frictionPoints.solutionPatterns.capabilities');

        return $this->show('Industry', $industry, [
            'parents' => collect(),
            'children' => $industry->workflows->pluck('department')->filter()->unique('id')->values(),
            'workflows' => $industry->workflows,
        ]);
    }

    public function companyType(CompanyType $companyType)
    {
        $companyType->load('workflows.industry', 'workflows.department', 'workflows.frictionPoints.solutionPatterns.capabilities');

        return $this->show('Company Type', $companyType, [
            'parents' => collect(),
            'children' => $companyType->workflows->pluck('department')->filter()->unique('id')->values(),
            'workflows' => $companyType->workflows,
        ]);
    }

    public function department(Department $department)
    {
        $department->load('workflows.industry', 'workflows.companyType', 'workflows.frictionPoints.solutionPatterns.capabilities');

        return $this->show('Department', $department, [
            'parents' => $department->workflows->pluck('industry')->merge($department->workflows->pluck('companyType'))->filter()->unique(fn ($item) => $item::class.'-'.$item->id)->values(),
            'children' => collect(),
            'workflows' => $department->workflows,
        ]);
    }

    public function workflow(Workflow $workflow)
    {
        $workflow->load('industry', 'companyType', 'department', 'frictionPoints.solutionPatterns.capabilities', 'articles', 'videos', 'services');

        return $this->show('Workflow', $workflow, [
            'parents' => collect([$workflow->industry, $workflow->companyType, $workflow->department])->filter()->values(),
            'children' => $workflow->frictionPoints,
            'workflows' => collect([$workflow]),
        ]);
    }

    public function frictionPoint(FrictionPoint $frictionPoint)
    {
        $frictionPoint->load('workflow.industry', 'workflow.companyType', 'workflow.department', 'solutionPatterns.capabilities');

        return $this->show('Friction Point', $frictionPoint, [
            'parents' => collect([$frictionPoint->workflow])->filter()->values(),
            'children' => $frictionPoint->solutionPatterns,
            'workflows' => collect([$frictionPoint->workflow])->filter()->values(),
            'frictionPoints' => collect([$frictionPoint]),
        ]);
    }

    public function solutionPattern(SolutionPattern $solutionPattern)
    {
        $solutionPattern->load('frictionPoints.workflow.industry', 'capabilities');

        return $this->show('Solution Pattern', $solutionPattern, [
            'parents' => $solutionPattern->frictionPoints,
            'children' => $solutionPattern->capabilities,
            'workflows' => $solutionPattern->frictionPoints->pluck('workflow')->filter()->unique('id')->values(),
            'frictionPoints' => $solutionPattern->frictionPoints,
            'solutionPatterns' => collect([$solutionPattern]),
        ]);
    }

    public function capability(Capability $capability)
    {
        $capability->load('solutionPatterns.frictionPoints.workflow');
        $solutionPatterns = $capability->solutionPatterns;
        $frictionPoints = $solutionPatterns->pluck('frictionPoints')->flatten()->unique('id')->values();

        return $this->show('Capability', $capability, [
            'parents' => $solutionPatterns,
            'children' => collect(),
            'workflows' => $frictionPoints->pluck('workflow')->filter()->unique('id')->values(),
            'frictionPoints' => $frictionPoints,
            'solutionPatterns' => $solutionPatterns,
            'capabilities' => collect([$capability]),
        ]);
    }

    private function show(string $type, object $record, array $related)
    {
        $workflows = $this->toCollection($related['workflows'] ?? collect())->unique('id')->values();
        $workflows->loadMissing([
            'articles' => fn ($query) => $query->published()->latest('published_at'),
            'videos' => fn ($query) => $query->published()->latest(),
            'services',
        ]);
        $frictionPoints = $this->toCollection($related['frictionPoints'] ?? $workflows->pluck('frictionPoints')->flatten())->unique('id')->values();
        $solutionPatterns = $this->toCollection($related['solutionPatterns'] ?? $frictionPoints->pluck('solutionPatterns')->flatten())->unique('id')->values();
        $capabilities = $this->toCollection($related['capabilities'] ?? $solutionPatterns->pluck('capabilities')->flatten())->unique('id')->values();

        return view('pages.atlas-detail', [
            'type' => $type,
            'record' => $record,
            'parents' => $this->toCollection($related['parents'] ?? collect()),
            'children' => $this->toCollection($related['children'] ?? collect()),
            'workflows' => $workflows,
            'frictionPoints' => $frictionPoints,
            'solutionPatterns' => $solutionPatterns,
            'capabilities' => $capabilities,
            'articles' => $workflows->pluck('articles')->flatten()->unique('id')->take(6)->values(),
            'videos' => $workflows->pluck('videos')->flatten()->unique('id')->take(6)->values(),
            'services' => $workflows->pluck('services')->flatten()->unique('id')->take(6)->values(),
            'assessmentPaths' => $workflows->pluck('assessment_path')->filter()->unique()->values(),
        ]);
    }

    private function toCollection(mixed $items): Collection
    {
        return $items instanceof EloquentCollection ? $items->toBase() : collect($items)->filter()->values();
    }
}

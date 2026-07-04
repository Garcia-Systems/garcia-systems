<?php

namespace Tests\Feature;

use App\Models\Capability;
use App\Models\CompanyType;
use App\Models\Department;
use App\Models\FrictionPoint;
use App\Models\Industry;
use App\Models\SolutionPattern;
use App\Models\Workflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AtlasDetailPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_industry_detail_page_is_public(): void
    {
        ['industry' => $industry] = $this->atlasGraph();

        $this->get(route('atlas.industries.show', $industry->slug))
            ->assertOk()
            ->assertSee('Opportunity Atlas')
            ->assertSee('Healthcare')
            ->assertSee('Patient Intake');
    }

    public function test_company_type_detail_page_is_public(): void
    {
        ['companyType' => $companyType] = $this->atlasGraph();

        $this->get(route('atlas.company-types.show', $companyType->slug))
            ->assertOk()
            ->assertSee('Multi-site Operator')
            ->assertSee('Patient Intake');
    }

    public function test_department_detail_page_is_public(): void
    {
        ['department' => $department] = $this->atlasGraph();

        $this->get(route('atlas.departments.show', $department->slug))
            ->assertOk()
            ->assertSee('Operations')
            ->assertSee('Healthcare');
    }

    public function test_workflow_detail_page_is_public(): void
    {
        ['workflow' => $workflow] = $this->atlasGraph();

        $this->get(route('atlas.workflows.show', $workflow->slug))
            ->assertOk()
            ->assertSee('Patient Intake')
            ->assertSee('Duplicate entry');
    }

    public function test_friction_point_detail_page_is_public(): void
    {
        ['frictionPoint' => $frictionPoint] = $this->atlasGraph();

        $this->get(route('atlas.friction-points.show', $frictionPoint->slug))
            ->assertOk()
            ->assertSee('Duplicate entry')
            ->assertSee('Structured intake');
    }

    public function test_solution_pattern_detail_page_is_public(): void
    {
        ['solutionPattern' => $solutionPattern] = $this->atlasGraph();

        $this->get(route('atlas.solution-patterns.show', $solutionPattern->slug))
            ->assertOk()
            ->assertSee('Structured intake')
            ->assertSee('Form automation');
    }

    public function test_capability_detail_page_is_public(): void
    {
        ['capability' => $capability] = $this->atlasGraph();

        $this->get(route('atlas.capabilities.show', $capability->slug))
            ->assertOk()
            ->assertSee('Form automation')
            ->assertSee('Structured intake');
    }

    public function test_atlas_cards_link_to_detail_pages(): void
    {
        ['workflow' => $workflow, 'frictionPoint' => $frictionPoint, 'solutionPattern' => $solutionPattern, 'capability' => $capability] = $this->atlasGraph();

        $this->get(route('atlas'))
            ->assertOk()
            ->assertSee(route('atlas.workflows.show', $workflow->slug), false)
            ->assertSee(route('atlas.friction-points.show', $frictionPoint->slug), false)
            ->assertSee(route('atlas.solution-patterns.show', $solutionPattern->slug), false)
            ->assertSee(route('atlas.capabilities.show', $capability->slug), false);
    }

    private function atlasGraph(): array
    {
        $industry = Industry::create(['name' => 'Healthcare', 'slug' => 'healthcare', 'description' => 'Care delivery teams.']);
        $companyType = CompanyType::create(['name' => 'Multi-site Operator', 'slug' => 'multi-site-operator', 'description' => 'Distributed operating model.']);
        $department = Department::create(['name' => 'Operations', 'slug' => 'operations', 'description' => 'Operational teams.']);
        $workflow = Workflow::create([
            'industry_id' => $industry->id,
            'company_type_id' => $companyType->id,
            'department_id' => $department->id,
            'name' => 'Patient Intake',
            'slug' => 'patient-intake',
            'description' => 'Collect patient information before appointments.',
        ]);
        $frictionPoint = FrictionPoint::create([
            'workflow_id' => $workflow->id,
            'name' => 'Duplicate entry',
            'slug' => 'duplicate-entry',
            'description' => 'Teams re-key the same information.',
            'impact' => 'Slow response times',
        ]);
        $solutionPattern = SolutionPattern::create(['name' => 'Structured intake', 'slug' => 'structured-intake', 'description' => 'Normalize requests at the edge.']);
        $capability = Capability::create(['name' => 'Form automation', 'slug' => 'form-automation', 'description' => 'Capture and route structured data.']);

        $frictionPoint->solutionPatterns()->attach($solutionPattern);
        $solutionPattern->capabilities()->attach($capability);

        return compact('industry', 'companyType', 'department', 'workflow', 'frictionPoint', 'solutionPattern', 'capability');
    }
}

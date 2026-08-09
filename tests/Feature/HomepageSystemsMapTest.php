<?php

namespace Tests\Feature;

use App\Models\FrictionPoint;
use App\Models\Industry;
use App\Models\SolutionPattern;
use App\Models\Workflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageSystemsMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_an_atlas_relationship_from_existing_data(): void
    {
        $industry = Industry::create(['name' => 'Field Services', 'slug' => 'field-services', 'description' => 'Field operations.']);
        $workflow = Workflow::create(['industry_id' => $industry->id, 'name' => 'Dispatch Planning', 'slug' => 'dispatch-planning', 'description' => 'Plan daily dispatches.']);
        $friction = FrictionPoint::create(['workflow_id' => $workflow->id, 'name' => 'Manual Replanning', 'slug' => 'manual-replanning', 'description' => 'Changes require repeated manual work.']);
        $solution = SolutionPattern::create(['name' => 'Exception-led Dispatch', 'slug' => 'exception-led-dispatch', 'description' => 'Route exceptions to an owner.']);
        $friction->solutionPatterns()->attach($solution);

        $this->get('/')
            ->assertOk()
            ->assertSee('data-atlas-map', false)
            ->assertSee('Industries')
            ->assertSee('Workflows')
            ->assertSee('Friction')
            ->assertSee('Solution patterns')
            ->assertSee('Field Services')
            ->assertSee('Dispatch Planning')
            ->assertSee('Manual Replanning')
            ->assertSee('Exception-led Dispatch')
            ->assertSee('aria-pressed="false"', false)
            ->assertSee('Explore the Opportunity Atlas');
    }

    public function test_homepage_handles_an_empty_atlas_and_keeps_services_available(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Opportunity paths are being mapped.')
            ->assertSee('Product Discovery')
            ->assertSee('Solutions Engineering')
            ->assertSee('Workflow Modernization')
            ->assertSee('data-reveal', false)
            ->assertSee('Explore the Opportunity Atlas');
    }
}

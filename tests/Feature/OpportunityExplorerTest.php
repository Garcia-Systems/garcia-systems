<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpportunityExplorerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_explorer_loads_and_no_filters_returns_results(): void
    {
        $this->get('/opportunity-explorer')
            ->assertOk()
            ->assertSee('Opportunity Explorer')
            ->assertSee('Capability')
            ->assertSee('Workflow Visibility')
            ->assertSee('Suggested services');
    }

    public function test_filtering_by_industry_department_and_workflow_works(): void
    {
        $this->get('/opportunity-explorer?industry=healthcare&department=clinical-operations&workflow=patient-intake-follow-up')
            ->assertOk()
            ->assertSee('Patient intake follow-up')
            ->assertSee('Customer intake bottlenecks')
            ->assertSee('Intake Management')
            ->assertDontSee('Production reporting');
    }

    public function test_keyword_search_works_across_context_fields(): void
    {
        $this->get('/opportunity-explorer?search=Healthcare')
            ->assertOk()
            ->assertSee('Patient intake follow-up')
            ->assertSee('Records reconciliation')
            ->assertDontSee('Supplier replenishment');
    }

    public function test_capability_cards_display_related_content(): void
    {
        $this->get('/opportunity-explorer?capability=vendor-coordination')
            ->assertOk()
            ->assertSee('Vendor Coordination')
            ->assertSee('Vendor coordination hub')
            ->assertSee('Vendor Coordination Needs More Than Email Threads')
            ->assertSee('Vendor Coordination Hub Overview')
            ->assertSee('Technical Liaison Services');
    }

    public function test_articles_and_videos_appear(): void
    {
        $this->get('/opportunity-explorer?search=inventory')
            ->assertOk()
            ->assertSee('Suggested articles')
            ->assertSee('Inventory Visibility Without a Full Platform Rebuild')
            ->assertSee('Suggested videos')
            ->assertSee('Inventory Visibility Walkthrough');
    }

    public function test_empty_state_is_handled(): void
    {
        $this->get('/opportunity-explorer?search=zzzz-not-real')
            ->assertOk()
            ->assertSee('No opportunity paths found')
            ->assertSee('View all paths');
    }
}

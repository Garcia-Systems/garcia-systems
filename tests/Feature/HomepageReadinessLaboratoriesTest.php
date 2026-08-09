<?php

namespace Tests\Feature;

use App\Models\AssessmentQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class HomepageReadinessLaboratoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_real_readiness_dimensions_and_assessment_link(): void
    {
        foreach ([
            ['key' => 'workflow_documentation', 'category' => 'Workflow documentation', 'question' => 'Do you have clearly documented workflows?', 'sort_order' => 1],
            ['key' => 'data_readiness', 'category' => 'Data readiness', 'question' => 'Is your operational data organized and accessible?', 'sort_order' => 2],
            ['key' => 'pilot_selection', 'category' => 'Pilot selection', 'question' => 'Can your team define measurable success?', 'sort_order' => 3],
            ['key' => 'stakeholder_alignment', 'category' => 'Stakeholder alignment', 'question' => 'Can process owners support implementation?', 'sort_order' => 4],
        ] as $question) {
            AssessmentQuestion::create($question + ['is_active' => true]);
        }

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Assessment preview')
            ->assertSee('No score assigned')
            ->assertSee('Workflow documentation')
            ->assertSee('Data readiness')
            ->assertSee('Pilot selection')
            ->assertSee('Stakeholder alignment')
            ->assertSee('Take the AI Readiness Assessment')
            ->assertSee('href="'.route('assessment').'"', false);
    }

    public function test_homepage_uses_readiness_fallback_when_questions_are_unavailable(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Workflow documentation')
            ->assertSee('Complete the assessment to calculate your score and readiness tier.');
    }

    public function test_homepage_renders_featured_executable_laboratories_and_links(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Executable laboratories')
            ->assertSee('Opportunity Atlas Laboratory')
            ->assertSee('AI Readiness Decision Instrument')
            ->assertSee('Knowledge Publishing System')
            ->assertSee('href="'.route('atlas').'"', false)
            ->assertSee('href="'.route('articles.index').'"', false);
    }

    public function test_homepage_has_a_useful_empty_laboratory_state(): void
    {
        Config::set('garcia.featured_laboratories', []);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Laboratory exhibits are being prepared.')
            ->assertSee('Explore tools');
    }
}

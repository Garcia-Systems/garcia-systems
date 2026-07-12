<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResponse;
use App\Models\ContentInstallationItem;
use App\Models\ContentInstallationRun;
use App\Models\FrictionPoint;
use App\Models\Industry;
use App\Models\Workflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GarciaContentCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_installs_assessment_questions_with_a_completed_manifest_and_rolls_them_back(): void
    {
        $this->artisan('garcia:content', ['action' => 'install', '--dataset' => 'assessment'])->assertExitCode(0);

        $run = ContentInstallationRun::first();

        $this->assertSame('completed', $run->status);
        $this->assertSame(4, AssessmentQuestion::where('is_active', true)->count());
        $this->assertSame(4, ContentInstallationItem::where('action', 'created')->count());

        $this->artisan('garcia:content', ['action' => 'rollback', '--run' => $run->uuid])->assertExitCode(0);

        $this->assertSame(0, AssessmentQuestion::count());
        $this->assertSame('rolled_back', $run->fresh()->status);
    }

    public function test_it_is_idempotent_and_records_reused_assessment_questions_without_overwriting_edits(): void
    {
        $this->artisan('garcia:content', ['action' => 'install', '--dataset' => 'assessment'])->assertExitCode(0);

        AssessmentQuestion::where('key', 'data_readiness')->first()->update(['question' => 'Admin edited question?']);

        $this->artisan('garcia:content', ['action' => 'install', '--dataset' => 'assessment'])->assertExitCode(0);

        $this->assertSame(4, AssessmentQuestion::count());
        $this->assertSame('Admin edited question?', AssessmentQuestion::where('key', 'data_readiness')->first()->question);
        $this->assertGreaterThanOrEqual(4, ContentInstallationItem::where('action', 'reused')->count());
    }

    public function test_dry_run_does_not_create_runs_or_content(): void
    {
        $this->artisan('garcia:content', ['action' => 'install', '--dataset' => 'assessment', '--dry-run' => true])
            ->expectsOutputToContain('Dry run only')
            ->assertExitCode(0);

        $this->assertSame(0, ContentInstallationRun::count());
        $this->assertSame(0, AssessmentQuestion::count());
    }

    public function test_it_installs_atlas_examples_and_rollback_preserves_reused_lookup_records(): void
    {
        $industry = Industry::create(['slug' => 'e-commerce', 'name' => 'Edited commerce', 'description' => 'Manual']);

        $this->artisan('garcia:content', ['action' => 'install', '--dataset' => 'atlas'])->assertExitCode(0);

        $run = ContentInstallationRun::first();

        $this->assertSame('completed', $run->status);
        $this->assertSame($industry->id, Industry::where('slug', 'e-commerce')->first()->id);
        $this->assertSame(12, Workflow::count());
        $this->assertSame(12, FrictionPoint::count());
        $this->assertGreaterThan(0, ContentInstallationItem::where('action', 'attached')->count());

        $this->artisan('garcia:content', ['action' => 'rollback', '--run' => $run->uuid])->assertExitCode(0);

        $this->assertTrue(Industry::where('slug', 'e-commerce')->exists());
        $this->assertSame(0, Workflow::count());
    }

    public function test_it_deactivates_referenced_installed_assessment_questions_instead_of_deleting_history(): void
    {
        $this->artisan('garcia:content', ['action' => 'install', '--dataset' => 'assessment'])->assertExitCode(0);

        $run = ContentInstallationRun::first();
        $question = AssessmentQuestion::first();
        $assessment = Assessment::create(['score' => 4, 'result_tier' => 'Early']);

        AssessmentResponse::create([
            'assessment_id' => $assessment->id,
            'assessment_question_id' => $question->id,
            'score' => 4,
        ]);

        $this->artisan('garcia:content', ['action' => 'rollback', '--latest' => true])->assertExitCode(0);

        $this->assertFalse($question->fresh()->is_active);
        $this->assertTrue(AssessmentResponse::where('assessment_question_id', $question->id)->exists());
        $this->assertSame('rolled_back', $run->fresh()->status);
    }

    public function test_it_hides_disabled_public_features_from_navigation_and_routes(): void
    {
        Config::set('garcia.features.ai_assessment', false);
        Config::set('garcia.features.opportunity_atlas', false);

        $this->get('/tools')->assertOk()->assertDontSee('Open tool')->assertDontSee('Explore');
        $this->get('/ai-readiness-assessment')->assertNotFound();
        $this->get('/opportunity-atlas')->assertNotFound();
    }
}

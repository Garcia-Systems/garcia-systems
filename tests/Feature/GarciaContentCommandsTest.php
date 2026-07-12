<?php

use App\Models\AssessmentQuestion;
use App\Models\ContentInstallationItem;
use App\Models\ContentInstallationRun;
use App\Models\FrictionPoint;
use App\Models\Industry;
use App\Models\Workflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

it('installs assessment questions with a completed manifest and rolls them back', function () {
    $this->artisan('garcia:content install --dataset=assessment')->assertExitCode(0);

    $run = ContentInstallationRun::first();
    expect($run->status)->toBe('completed')
        ->and(AssessmentQuestion::where('is_active', true)->count())->toBe(4)
        ->and(ContentInstallationItem::where('action', 'created')->count())->toBe(4);

    $this->artisan('garcia:content rollback --run='.$run->uuid)->assertExitCode(0);

    expect(AssessmentQuestion::count())->toBe(0)
        ->and($run->fresh()->status)->toBe('rolled_back');
});

it('is idempotent and records reused assessment questions without overwriting edits', function () {
    $this->artisan('garcia:content install --dataset=assessment')->assertExitCode(0);
    AssessmentQuestion::where('key', 'data_readiness')->first()->update(['question' => 'Admin edited question?']);

    $this->artisan('garcia:content install --dataset=assessment')->assertExitCode(0);

    expect(AssessmentQuestion::count())->toBe(4)
        ->and(AssessmentQuestion::where('key', 'data_readiness')->first()->question)->toBe('Admin edited question?')
        ->and(ContentInstallationItem::where('action', 'reused')->count())->toBeGreaterThanOrEqual(4);
});

it('dry-runs without creating runs or content', function () {
    $this->artisan('garcia:content install --dataset=assessment --dry-run')
        ->expectsOutputToContain('Dry run only')
        ->assertExitCode(0);

    expect(ContentInstallationRun::count())->toBe(0)
        ->and(AssessmentQuestion::count())->toBe(0);
});

it('installs atlas examples and rollback preserves reused lookup records', function () {
    $industry = Industry::create(['slug' => 'e-commerce', 'name' => 'Edited commerce', 'description' => 'Manual']);

    $this->artisan('garcia:content install --dataset=atlas')->assertExitCode(0);

    $run = ContentInstallationRun::first();
    expect($run->status)->toBe('completed')
        ->and(Industry::where('slug', 'e-commerce')->first()->id)->toBe($industry->id)
        ->and(Workflow::count())->toBe(12)
        ->and(FrictionPoint::count())->toBe(12)
        ->and(ContentInstallationItem::where('action', 'attached')->count())->toBeGreaterThan(0);

    $this->artisan('garcia:content rollback --run='.$run->uuid)->assertExitCode(0);

    expect(Industry::where('slug', 'e-commerce')->exists())->toBeTrue()
        ->and(Workflow::count())->toBe(0);
});



it('deactivates referenced installed assessment questions instead of deleting history', function () {
    $this->artisan('garcia:content install --dataset=assessment')->assertExitCode(0);
    $run = ContentInstallationRun::first();
    $question = AssessmentQuestion::first();
    $assessment = \App\Models\Assessment::create(['score' => 4, 'result_tier' => 'Early']);
    \App\Models\AssessmentResponse::create(['assessment_id' => $assessment->id, 'assessment_question_id' => $question->id, 'score' => 4]);

    $this->artisan('garcia:content rollback --latest')->assertExitCode(0);

    expect($question->fresh()->is_active)->toBeFalse()
        ->and(\App\Models\AssessmentResponse::where('assessment_question_id', $question->id)->exists())->toBeTrue()
        ->and($run->fresh()->status)->toBe('rolled_back');
});

it('hides disabled public features from navigation and routes', function () {
    Config::set('garcia.features.ai_assessment', false);
    Config::set('garcia.features.opportunity_atlas', false);

    $this->get('/tools')->assertOk()->assertDontSee('Open tool')->assertDontSee('Explore');
    $this->get('/ai-readiness-assessment')->assertNotFound();
    $this->get('/opportunity-atlas')->assertNotFound();
});

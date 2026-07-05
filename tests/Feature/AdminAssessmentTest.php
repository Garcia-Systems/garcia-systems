<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAssessmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_cannot_access_assessment_admin(): void
    {
        $this->get(route('admin.assessment-questions.index'))->assertRedirect('/login');
        $this->get(route('admin.assessment-submissions.index'))->assertRedirect('/login');
    }

    public function test_authenticated_users_can_manage_questions(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.assessment-questions.store'), [
            'question' => 'Do teams own key workflow data?',
            'help_text' => 'Rate current ownership clarity.',
            'category' => 'Data',
            'sort_order' => 2,
            'weight' => 1.5,
            'is_active' => 1,
        ]);

        $question = AssessmentQuestion::where('question', 'Do teams own key workflow data?')->firstOrFail();
        $response->assertRedirect(route('admin.assessment-questions.edit', $question));

        $this->actingAs($user)->put(route('admin.assessment-questions.update', $question), [
            'question' => 'Updated readiness question?',
            'help_text' => 'Updated help.',
            'category' => 'Workflow',
            'sort_order' => 5,
            'weight' => 2,
            'is_active' => 0,
        ])->assertRedirect(route('admin.assessment-questions.edit', $question));

        $this->assertDatabaseHas('assessment_questions', [
            'id' => $question->id,
            'question' => 'Updated readiness question?',
            'category' => 'Workflow',
            'sort_order' => 5,
            'weight' => 2,
            'is_active' => false,
        ]);

        $this->actingAs($user)->patch(route('admin.assessment-questions.reorder'), [
            'orders' => [$question->id => 1],
        ])->assertRedirect(route('admin.assessment-questions.index'));

        $this->assertDatabaseHas('assessment_questions', ['id' => $question->id, 'sort_order' => 1]);
    }

    public function test_inactive_questions_do_not_appear_publicly(): void
    {
        AssessmentQuestion::create(['question' => 'Active question', 'sort_order' => 1, 'weight' => 1, 'is_active' => true]);
        AssessmentQuestion::create(['question' => 'Inactive question', 'sort_order' => 2, 'weight' => 1, 'is_active' => false]);

        $this->get(route('assessment'))
            ->assertOk()
            ->assertSee('Active question')
            ->assertDontSee('Inactive question');
    }

    public function test_submitted_assessments_can_be_reviewed_in_admin(): void
    {
        $user = User::factory()->create();
        $question = AssessmentQuestion::create(['question' => 'Reviewable question', 'category' => 'Process', 'sort_order' => 1, 'weight' => 1, 'is_active' => true]);
        $assessment = Assessment::create([
            'name' => 'Jane Garcia',
            'email' => 'jane@example.com',
            'company' => 'Garcia Systems',
            'score' => 4,
            'result_tier' => 'Foundation in progress',
            'summary' => 'Start with one workflow.',
        ]);
        AssessmentResponse::create([
            'assessment_id' => $assessment->id,
            'assessment_question_id' => $question->id,
            'score' => 4,
        ]);

        $this->actingAs($user)->get(route('admin.assessment-submissions.index'))
            ->assertOk()
            ->assertSee('Jane Garcia')
            ->assertSee('jane@example.com')
            ->assertSee('Garcia Systems')
            ->assertSee('Foundation in progress');

        $this->actingAs($user)->get(route('admin.assessment-submissions.show', $assessment))
            ->assertOk()
            ->assertSee('Reviewable question')
            ->assertSee('Start with one workflow.')
            ->assertSee('Score: 4');
    }
}

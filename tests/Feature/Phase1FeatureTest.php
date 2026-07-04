<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Capability;
use App\Models\CompanyType;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResponse;
use App\Models\Category;
use App\Models\ContactSubmission;
use App\Models\FrictionPoint;
use App\Models\Industry;
use App\Models\Department;
use App\Models\SolutionPattern;
use App\Models\Video;
use App\Models\Workflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase1FeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_returns_successfully_and_contains_positioning_text(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Business-first systems consulting')
            ->assertSee('Turning Business Problems Into Products, Systems, and Intelligent Workflows')
            ->assertSee('practical enough to adopt and specific enough to measure');
    }

    public function test_services_page_returns_successfully(): void
    {
        $this->get('/services')
            ->assertOk()
            ->assertSee('Services');
    }

    public function test_services_page_renders_expanded_service_offerings_and_conversion_paths(): void
    {
        $this->get('/services')
            ->assertOk()
            ->assertSee('Product Discovery')
            ->assertSee('Solutions Engineering')
            ->assertSee('Workflow Modernization')
            ->assertSee('Technical Liaison Services')
            ->assertSee('AI Opportunity Assessment')
            ->assertSee('Product Execution Support')
            ->assertSee('Overview')
            ->assertSee('Business problems addressed')
            ->assertSee('Expected outcomes')
            ->assertSee('Deliverables')
            ->assertSee('Ideal clients')
            ->assertSee('Example engagements')
            ->assertSee('How Garcia Systems Works')
            ->assertSee('Discover')
            ->assertSee('Analyze')
            ->assertSee('Validate')
            ->assertSee('Measure')
            ->assertSee('Iterate')
            ->assertSee('Best fit')
            ->assertSee('Not best fit')
            ->assertSee('href="'.route('contact').'"', false);
    }

    public function test_homepage_renders_service_cta_blocks(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Services summary')
            ->assertSee('Product Discovery')
            ->assertSee('Workflow Modernization')
            ->assertSee('AI Opportunity Assessment')
            ->assertSee('Solutions Engineering')
            ->assertSee('Technical Liaison Services')
            ->assertSee('Product Execution Support')
            ->assertSee('Opportunity Atlas')
            ->assertSee('Latest Thinking')
            ->assertSee('Featured videos')
            ->assertSee('Explore AI Readiness');
    }

    public function test_articles_index_returns_successfully_and_displays_sample_articles(): void
    {
        $category = Category::create([
            'name' => 'Strategy',
            'slug' => 'strategy',
            'description' => 'Practical AI and automation planning.',
        ]);

        Article::create([
            'category_id' => $category->id,
            'title' => 'Finding Practical Automation Opportunities',
            'slug' => 'finding-practical-automation-opportunities',
            'excerpt' => 'Start with recurring friction and measurable delays.',
            'body' => 'Meaningful automation work begins with operational pain that can be observed and measured.',
            'published_at' => now(),
        ]);

        Article::create([
            'category_id' => $category->id,
            'title' => 'AI Readiness for Growing Teams',
            'slug' => 'ai-readiness-for-growing-teams',
            'excerpt' => 'Clarify ownership, data quality, workflow stability, and risk tolerance.',
            'body' => 'Readiness is created through better workflows, cleaner data, and clear success metrics.',
            'published_at' => now()->subDay(),
        ]);

        $this->get('/articles')
            ->assertOk()
            ->assertSee('Finding Practical Automation Opportunities')
            ->assertSee('AI Readiness for Growing Teams')
            ->assertSee('Strategy');
    }


    public function test_articles_only_show_published_and_current_content(): void
    {
        $category = Category::create([
            'name' => 'Strategy',
            'slug' => 'strategy',
            'description' => 'Practical AI and automation planning.',
        ]);

        $published = Article::create([
            'category_id' => $category->id,
            'title' => 'Published Article',
            'slug' => 'published-article',
            'excerpt' => 'Visible article excerpt.',
            'body' => 'Visible article body.',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $draft = Article::create([
            'category_id' => $category->id,
            'title' => 'Draft Article',
            'slug' => 'draft-article',
            'excerpt' => 'Hidden draft excerpt.',
            'body' => 'Hidden draft body.',
            'is_published' => false,
            'published_at' => now()->subDay(),
        ]);

        $future = Article::create([
            'category_id' => $category->id,
            'title' => 'Future Article',
            'slug' => 'future-article',
            'excerpt' => 'Hidden future excerpt.',
            'body' => 'Hidden future body.',
            'is_published' => true,
            'published_at' => now()->addDay(),
        ]);

        $this->get('/articles')
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee($draft->title)
            ->assertDontSee($future->title);

        $this->get(route('articles.show', $published))->assertOk()->assertSee($published->title);
        $this->get(route('articles.show', $draft))->assertNotFound();
        $this->get(route('articles.show', $future))->assertNotFound();
    }

    public function test_homepage_only_previews_published_current_articles_and_published_videos(): void
    {
        Article::create([
            'title' => 'Homepage Published Article',
            'slug' => 'homepage-published-article',
            'excerpt' => 'Visible homepage article.',
            'body' => 'Visible homepage article body.',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        Article::create([
            'title' => 'Homepage Draft Article',
            'slug' => 'homepage-draft-article',
            'excerpt' => 'Hidden homepage draft.',
            'body' => 'Hidden homepage draft body.',
            'is_published' => false,
            'published_at' => now()->subDay(),
        ]);

        Video::create([
            'title' => 'Homepage Published Video',
            'slug' => 'homepage-published-video',
            'url' => 'https://example.com/videos/published',
            'description' => 'Visible homepage video.',
            'is_published' => true,
        ]);

        Video::create([
            'title' => 'Homepage Draft Video',
            'slug' => 'homepage-draft-video',
            'url' => 'https://example.com/videos/draft',
            'description' => 'Hidden homepage video.',
            'is_published' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Homepage Published Article')
            ->assertDontSee('Homepage Draft Article')
            ->assertSee('Homepage Published Video')
            ->assertDontSee('Homepage Draft Video');
    }

    public function test_videos_page_only_shows_published_videos(): void
    {
        Video::create([
            'title' => 'Published Video',
            'slug' => 'published-video',
            'url' => 'https://example.com/videos/published',
            'description' => 'Visible video.',
            'is_published' => true,
        ]);

        Video::create([
            'title' => 'Draft Video',
            'slug' => 'draft-video',
            'url' => 'https://example.com/videos/draft',
            'description' => 'Hidden video.',
            'is_published' => false,
        ]);

        $this->get('/videos')
            ->assertOk()
            ->assertSee('Published Video')
            ->assertDontSee('Draft Video');
    }

    public function test_opportunity_atlas_returns_successfully_and_displays_seeded_content(): void
    {
        $industry = Industry::create([
            'name' => 'Professional services',
            'slug' => 'professional-services',
            'description' => 'Sample industry for opportunity mapping.',
        ]);

        $workflow = Workflow::create([
            'industry_id' => $industry->id,
            'name' => 'Client intake and follow-up',
            'slug' => 'client-intake-follow-up',
            'description' => 'Capture requests, qualify needs, route work, and follow up consistently.',
        ]);

        FrictionPoint::create([
            'workflow_id' => $workflow->id,
            'name' => 'Manual status chasing',
            'slug' => 'manual-status-chasing',
            'description' => 'Team members spend time asking where requests stand instead of moving work forward.',
            'impact' => 'Slower response times and lower visibility.',
        ]);

        $this->get('/opportunity-atlas')
            ->assertOk()
            ->assertSee('Professional services')
            ->assertSee('Client intake and follow-up')
            ->assertSee('Manual status chasing');
    }


    public function test_opportunity_atlas_filters_by_query_parameters(): void
    {
        $this->createAtlasExample('Healthcare', 'Growing mid-market team', 'Operations', 'Inventory visibility', 'Manual Reporting', 'Automation', 'Operational dashboard');
        $this->createAtlasExample('Education', 'Public agency', 'Compliance', 'Grant documentation', 'Knowledge Silos', 'Knowledge Management', 'Shared knowledge base');

        $this->get('/opportunity-atlas?industry=healthcare')
            ->assertOk()
            ->assertSee('Healthcare')
            ->assertSee('Inventory visibility')
            ->assertDontSee('Grant documentation');

        $this->get('/opportunity-atlas?department=operations')
            ->assertOk()
            ->assertSee('Inventory visibility')
            ->assertDontSee('Grant documentation');

        $this->get('/opportunity-atlas?workflow=inventory-visibility')
            ->assertOk()
            ->assertSee('Manual Reporting')
            ->assertDontSee('Knowledge Silos');

        $this->get('/opportunity-atlas?capability=automation')
            ->assertOk()
            ->assertSee('Operational dashboard')
            ->assertDontSee('Shared knowledge base');
    }

    public function test_opportunity_atlas_combines_filters(): void
    {
        $this->createAtlasExample('Healthcare', 'Growing mid-market team', 'Operations', 'Inventory visibility', 'Manual Reporting', 'Automation', 'Operational dashboard');
        $this->createAtlasExample('Healthcare', 'Public agency', 'Compliance', 'Records reconciliation', 'Data Quality', 'Reporting', 'Data cleanup workflow');

        $this->get('/opportunity-atlas?industry=healthcare&department=operations&capability=automation')
            ->assertOk()
            ->assertSee('Inventory visibility')
            ->assertDontSee('Records reconciliation');
    }

    public function test_opportunity_atlas_empty_filters_return_all_results(): void
    {
        $this->createAtlasExample('Healthcare', 'Growing mid-market team', 'Operations', 'Inventory visibility', 'Manual Reporting', 'Automation', 'Operational dashboard');
        $this->createAtlasExample('Retail', 'Small business', 'Procurement', 'Supplier replenishment', 'Vendor Coordination', 'Vendor Coordination', 'Vendor coordination hub');

        $this->get('/opportunity-atlas?industry=&department=&workflow=')
            ->assertOk()
            ->assertSee('Inventory visibility')
            ->assertSee('Supplier replenishment')
            ->assertSee('No filters active');
    }

    public function test_opportunity_atlas_invalid_filters_fail_gracefully(): void
    {
        $this->createAtlasExample('Healthcare', 'Growing mid-market team', 'Operations', 'Inventory visibility', 'Manual Reporting', 'Automation', 'Operational dashboard');

        $this->get('/opportunity-atlas?industry=does-not-exist&capability=unknown')
            ->assertOk()
            ->assertSee('No atlas results yet')
            ->assertDontSee('Inventory visibility');
    }

    private function createAtlasExample(string $industryName, string $companyTypeName, string $departmentName, string $workflowName, string $frictionName, string $capabilityName, string $patternName): void
    {
        $industry = Industry::firstOrCreate(['slug' => str($industryName)->slug()], ['name' => $industryName, 'description' => 'Test industry.']);
        $companyType = CompanyType::firstOrCreate(['slug' => str($companyTypeName)->slug()], ['name' => $companyTypeName, 'description' => 'Test company type.']);
        $department = Department::firstOrCreate(['slug' => str($departmentName)->slug()], ['name' => $departmentName, 'description' => 'Test department.']);
        $capability = Capability::firstOrCreate(['slug' => str($capabilityName)->slug()], ['name' => $capabilityName, 'description' => 'Test capability.']);
        $pattern = SolutionPattern::firstOrCreate(['slug' => str($patternName)->slug()], ['name' => $patternName, 'description' => 'Test pattern.']);
        $pattern->capabilities()->attach($capability);
        $workflow = Workflow::create([
            'industry_id' => $industry->id,
            'company_type_id' => $companyType->id,
            'department_id' => $department->id,
            'name' => $workflowName,
            'slug' => str($workflowName)->slug(),
            'description' => 'Test workflow.',
        ]);
        $friction = FrictionPoint::create([
            'workflow_id' => $workflow->id,
            'name' => $frictionName,
            'slug' => str($frictionName)->slug(),
            'description' => 'Test friction.',
            'impact' => 'Test impact.',
        ]);
        $friction->solutionPatterns()->attach($pattern);
    }

    public function test_contact_page_returns_successfully(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertSee('Contact')
            ->assertSee('Send message');
    }

    public function test_contact_form_stores_a_contact_submission(): void
    {
        $payload = [
            'name' => 'Avery Garcia',
            'email' => 'avery@example.com',
            'company' => 'Garcia Demo Co',
            'service_interest' => 'Workflow automation MVP',
            'message' => 'We want to reduce manual intake and status chasing.',
        ];

        $this->from('/contact')->post('/contact', $payload)
            ->assertRedirect('/contact')
            ->assertSessionHas('status', 'Thanks — your message has been saved.');

        $this->assertDatabaseHas(ContactSubmission::class, $payload);
    }


    public function test_contact_form_validation_errors_are_rendered_and_do_not_persist(): void
    {
        $this->from('/contact')->post('/contact', [
            'name' => '',
            'email' => 'not-an-email',
            'message' => '',
        ])
            ->assertRedirect('/contact')
            ->assertSessionHasErrors(['name', 'email', 'message']);

        $this->assertDatabaseCount(ContactSubmission::class, 0);

        $this->get('/contact')
            ->assertOk()
            ->assertSee('Please fix the highlighted fields and try again.');
    }

    public function test_ai_readiness_assessment_page_returns_successfully(): void
    {
        AssessmentQuestion::create([
            'question' => 'Do you have clearly documented workflows?',
            'help_text' => 'Use your current operating reality, not an ideal future state.',
            'sort_order' => 1,
        ]);

        $this->get('/ai-readiness-assessment')
            ->assertOk()
            ->assertSee('AI Readiness Assessment')
            ->assertSee('Do you have clearly documented workflows?');
    }


    public function test_assessment_rejects_unknown_question_keys_out_of_range_values_and_malformed_payloads(): void
    {
        $question = AssessmentQuestion::create([
            'question' => 'Do you have clearly documented workflows?',
            'help_text' => 'Use your current operating reality, not an ideal future state.',
            'sort_order' => 1,
        ]);

        $this->from('/ai-readiness-assessment')->post('/ai-readiness-assessment', [
            'responses' => [
                $question->id => 6,
                9999 => 3,
            ],
        ])
            ->assertRedirect('/ai-readiness-assessment')
            ->assertSessionHasErrors(['responses', 'responses.'.$question->id]);

        $this->from('/ai-readiness-assessment')->post('/ai-readiness-assessment', [
            'responses' => 'malformed',
        ])
            ->assertRedirect('/ai-readiness-assessment')
            ->assertSessionHasErrors(['responses']);

        $this->assertDatabaseCount(Assessment::class, 0);
        $this->assertDatabaseCount(AssessmentResponse::class, 0);
    }

    public function test_assessment_submission_stores_assessment_responses_score_and_shows_result_page(): void
    {
        $questions = collect([
            'Do you have clearly documented workflows?',
            'Is your operational data organized and accessible?',
            'Can your team define measurable success for an AI or automation pilot?',
            'Do process owners have time to support implementation?',
        ])->map(fn (string $question, int $index) => AssessmentQuestion::create([
            'question' => $question,
            'help_text' => 'Use your current operating reality, not an ideal future state.',
            'sort_order' => $index + 1,
        ]));

        $responses = [
            $questions[0]->id => 5,
            $questions[1]->id => 4,
            $questions[2]->id => 4,
            $questions[3]->id => 3,
        ];

        $response = $this->post('/ai-readiness-assessment', [
            'name' => 'Morgan Lee',
            'email' => 'morgan@example.com',
            'company' => 'Readiness Co',
            'responses' => $responses,
        ]);

        $assessment = Assessment::query()->sole();

        $response->assertRedirect(route('assessment.result', $assessment));

        $this->assertSame(16, $assessment->score);
        $this->assertSame('Ready to prioritize pilots', $assessment->result_tier);
        $this->assertDatabaseCount(AssessmentResponse::class, 4);

        foreach ($responses as $questionId => $score) {
            $this->assertDatabaseHas(AssessmentResponse::class, [
                'assessment_id' => $assessment->id,
                'assessment_question_id' => $questionId,
                'score' => $score,
            ]);
        }

        $this->get(route('assessment.result', $assessment))
            ->assertOk()
            ->assertSee('Your readiness result')
            ->assertSee('Score: 16')
            ->assertSee('Ready to prioritize pilots');
    }
}

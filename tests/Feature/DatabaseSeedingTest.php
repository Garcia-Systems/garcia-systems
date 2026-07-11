<?php

namespace Tests\Feature;

use App\Models\{Article,AssessmentQuestion,Capability,Category,FrictionPoint,SolutionPattern,Tag,User,Video,Workflow};
use Database\Seeders\{AdministratorSeeder,LookupReferenceSeeder,StarterPublicContentSeeder};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{DB,Hash};
use Tests\TestCase;

class DatabaseSeedingTest extends TestCase
{
    use RefreshDatabase;

    public function test_lookup_and_starter_content_seeders_are_idempotent(): void
    {
        $this->seed([LookupReferenceSeeder::class, StarterPublicContentSeeder::class]);

        $counts = $this->contentCounts();
        $article = Article::where('slug', 'how-to-find-automation-opportunities-without-chasing-hype')->firstOrFail();
        $friction = FrictionPoint::where('slug', 'customer-intake-bottlenecks')->firstOrFail();
        $pattern = SolutionPattern::where('slug', 'structured-intake-and-routing')->firstOrFail();
        $articleTagCount = $article->tags()->count();
        $frictionPatternCount = $friction->solutionPatterns()->count();
        $patternCapabilityCount = $pattern->capabilities()->count();
        $articleTagPivotCount = DB::table('article_tag')->count();
        $frictionPatternPivotCount = DB::table('friction_point_solution_pattern')->count();
        $capabilityPatternPivotCount = DB::table('capability_solution_pattern')->count();

        $this->seed([LookupReferenceSeeder::class, StarterPublicContentSeeder::class]);

        $this->assertSame($counts, $this->contentCounts());
        $this->assertSame($articleTagCount, $article->fresh()->tags()->count());
        $this->assertSame($frictionPatternCount, $friction->fresh()->solutionPatterns()->count());
        $this->assertSame($patternCapabilityCount, $pattern->fresh()->capabilities()->count());
        $this->assertDatabaseCount('article_tag', $articleTagPivotCount);
        $this->assertDatabaseCount('friction_point_solution_pattern', $frictionPatternPivotCount);
        $this->assertDatabaseCount('capability_solution_pattern', $capabilityPatternPivotCount);
    }

    public function test_seeded_slug_records_remain_unique_after_repeated_runs(): void
    {
        $this->seed([LookupReferenceSeeder::class, StarterPublicContentSeeder::class]);
        $this->seed([LookupReferenceSeeder::class, StarterPublicContentSeeder::class]);

        foreach ([Category::class, Tag::class, Article::class, Capability::class, SolutionPattern::class, Workflow::class, FrictionPoint::class, Video::class] as $model) {
            $this->assertSame($model::count(), $model::distinct('slug')->count('slug'), $model.' slugs should be unique.');
        }
    }

    public function test_existing_administrator_password_is_not_changed_by_second_seed_run(): void
    {
        config(['services.admin.email' => 'admin@example.com', 'services.admin.password' => 'initial-secret', 'services.admin.name' => 'Initial Admin']);

        $this->seed(AdministratorSeeder::class);
        $admin = User::where('email', 'admin@example.com')->firstOrFail();
        $originalPassword = $admin->password;

        config(['services.admin.password' => 'changed-secret', 'services.admin.name' => 'Changed Admin']);
        $this->seed(AdministratorSeeder::class);

        $admin->refresh();
        $this->assertSame($originalPassword, $admin->password);
        $this->assertTrue(Hash::check('initial-secret', $admin->password));
        $this->assertSame('Initial Admin', $admin->name);
    }

    public function test_initial_administrator_can_be_created_from_configuration(): void
    {
        config(['services.admin.email' => 'new-admin@example.com', 'services.admin.password' => 'create-secret', 'services.admin.name' => 'Configured Admin']);

        $this->seed(AdministratorSeeder::class);

        $admin = User::where('email', 'new-admin@example.com')->firstOrFail();
        $this->assertSame('Configured Admin', $admin->name);
        $this->assertTrue(Hash::check('create-secret', $admin->password));
        $this->assertNotNull($admin->email_verified_at);
    }

    private function contentCounts(): array
    {
        return [
            'categories' => Category::count(),
            'tags' => Tag::count(),
            'articles' => Article::count(),
            'capabilities' => Capability::count(),
            'solution_patterns' => SolutionPattern::count(),
            'workflows' => Workflow::count(),
            'friction_points' => FrictionPoint::count(),
            'videos' => Video::count(),
            'assessment_questions' => AssessmentQuestion::count(),
        ];
    }
}

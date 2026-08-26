<?php

namespace Tests\Feature;

use App\Models\{Article, Category, Video};
use Database\Seeders\LookupReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RefreshPositioningContentCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_only_an_explicitly_managed_reference_record(): void
    {
        $this->seed(LookupReferenceSeeder::class);
        $category = Category::where('managed_content_key', 'category.strategy')->firstOrFail();
        $category->update(['description' => 'Outdated managed copy']);
        $category->forceFill([
            'managed_content_hash' => hash('sha256', json_encode(['name' => 'Strategy', 'description' => 'Outdated managed copy'], JSON_THROW_ON_ERROR)),
        ])->save();

        $this->artisan('garcia:refresh-positioning-content')
            ->expectsOutputToContain('Reference records updated: 1')
            ->assertSuccessful();

        $this->assertSame('Business-first options analysis across workflows, systems, economics, and implementation.', $category->fresh()->description);
    }

    public function test_a_managed_record_becomes_protected_after_manual_customization(): void
    {
        $this->seed(LookupReferenceSeeder::class);
        $category = Category::where('managed_content_key', 'category.strategy')->firstOrFail();
        $originalHash = $category->managed_content_hash;
        $category->update(['description' => 'Administrator-customized managed copy']);

        $this->artisan('garcia:refresh-positioning-content')
            ->expectsOutputToContain('Protected/customized records skipped: 1')
            ->assertSuccessful();

        $this->assertSame('Administrator-customized managed copy', $category->fresh()->description);
        $this->assertSame($originalHash, $category->fresh()->managed_content_hash);
    }

    public function test_a_managed_key_without_provenance_hash_is_treated_as_ambiguous(): void
    {
        $category = Category::create([
            'name' => 'Strategy',
            'slug' => 'strategy',
            'description' => 'Legacy production wording',
            'managed_content_key' => 'category.strategy',
        ]);

        $this->artisan('garcia:refresh-positioning-content')
            ->expectsOutputToContain('Protected/customized records skipped: 1')
            ->assertSuccessful();

        $this->assertSame('Legacy production wording', $category->fresh()->description);
        $this->assertNull($category->fresh()->managed_content_hash);
    }

    public function test_running_twice_is_idempotent_and_unrelated_records_are_unchanged(): void
    {
        $custom = Category::create(['name' => 'Administrator category', 'slug' => 'administrator-category', 'description' => 'Keep me']);

        $this->artisan('garcia:refresh-positioning-content')->assertSuccessful();
        $counts = $this->referenceCounts();
        $this->artisan('garcia:refresh-positioning-content')
            ->expectsOutputToContain('Reference records created: 0')
            ->expectsOutputToContain('Reference records updated: 0')
            ->assertSuccessful();

        $this->assertSame($counts, $this->referenceCounts());
        $this->assertSame('Keep me', $custom->fresh()->description);
        $this->assertNull($custom->managed_content_key);
    }

    public function test_articles_and_videos_including_customized_starter_slugs_are_never_modified(): void
    {
        $category = Category::create(['name' => 'Custom', 'slug' => 'custom']);
        $article = Article::create([
            'category_id' => $category->id,
            'title' => 'How to Find Automation Opportunities Without Chasing Hype',
            'slug' => 'how-to-find-automation-opportunities-without-chasing-hype',
            'excerpt' => 'Administrator excerpt',
            'body' => 'A production article body edited by an administrator.',
            'is_published' => false,
        ]);
        $video = Video::create([
            'title' => 'Mapping Workflow Friction',
            'slug' => 'mapping-workflow-friction',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'description' => 'Production description',
            'is_published' => false,
        ]);
        $unrelatedArticle = Article::create(['title' => 'Admin article', 'slug' => 'admin-article', 'excerpt' => 'Custom', 'body' => 'Custom body', 'is_published' => true]);
        $unrelatedVideo = Video::create(['title' => 'Admin video', 'slug' => 'admin-video', 'url' => 'https://example.com/video', 'description' => 'Custom video', 'is_published' => true]);

        $this->artisan('garcia:refresh-positioning-content')->assertSuccessful();

        $this->assertSame('A production article body edited by an administrator.', $article->fresh()->body);
        $this->assertFalse($article->fresh()->is_published);
        $this->assertSame('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $video->fresh()->url);
        $this->assertSame('Production description', $video->fresh()->description);
        $this->assertFalse($video->fresh()->is_published);
        $this->assertSame('Custom body', $unrelatedArticle->fresh()->body);
        $this->assertSame('https://example.com/video', $unrelatedVideo->fresh()->url);
    }

    public function test_an_unowned_slug_collision_is_protected_and_not_claimed(): void
    {
        $category = Category::create(['name' => 'Customized strategy', 'slug' => 'strategy', 'description' => 'Production-owned wording']);

        $this->artisan('garcia:refresh-positioning-content')
            ->expectsOutputToContain('Protected/customized records skipped: 1')
            ->assertSuccessful();

        $this->assertSame('Production-owned wording', $category->fresh()->description);
        $this->assertNull($category->fresh()->managed_content_key);
    }

    public function test_dry_run_reports_but_persists_nothing(): void
    {
        $this->artisan('garcia:refresh-positioning-content', ['--dry-run' => true])
            ->expectsOutputToContain('No database changes were made.')
            ->assertSuccessful();

        $this->assertSame(0, Category::count());
    }

    public function test_command_executes_no_destructive_sql(): void
    {
        $statements = [];
        DB::listen(function ($query) use (&$statements): void {
            $statements[] = strtolower($query->sql);
        });

        $this->artisan('garcia:refresh-positioning-content')->assertSuccessful();

        $destructive = array_filter($statements, fn (string $sql): bool => preg_match('/\b(delete|truncate|drop)\b/', $sql) === 1);
        $this->assertSame([], array_values($destructive));
    }

    private function referenceCounts(): array
    {
        return [
            'categories' => DB::table('categories')->count(),
            'capabilities' => DB::table('capabilities')->count(),
            'solution_patterns' => DB::table('solution_patterns')->count(),
            'industries' => DB::table('industries')->count(),
            'company_types' => DB::table('company_types')->count(),
            'departments' => DB::table('departments')->count(),
            'assessment_questions' => DB::table('assessment_questions')->count(),
        ];
    }
}

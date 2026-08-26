<?php

namespace Tests\Feature;

use App\Models\{Article, FrictionPoint, Industry, Video, Workflow};
use Database\Seeders\{LookupReferenceSeeder, StarterPublicContentSeeder};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RefreshAtlasContentCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_managed_examples_with_their_references_and_solution_patterns(): void
    {
        $this->seed(LookupReferenceSeeder::class);

        $this->artisan('garcia:refresh-atlas-content')
            ->expectsOutputToContain('Workflows created: 18')
            ->expectsOutputToContain('Friction points created: 18')
            ->expectsOutputToContain('Relationships changed: 18')
            ->assertSuccessful();

        $workflow = Workflow::where('slug', 'location-closeout-review')->firstOrFail();
        $this->assertSame('atlas.workflow.location-closeout-review', $workflow->managed_content_key);
        $this->assertSame('Restaurants', $workflow->industry->name);
        $this->assertSame('Multi-location operator', $workflow->companyType->name);
        $this->assertSame('Operations', $workflow->department->name);

        $friction = $workflow->frictionPoints()->where('slug', 'restaurant-closeout-reporting')->firstOrFail();
        $this->assertSame('atlas.friction-point.restaurant-closeout-reporting', $friction->managed_content_key);
        $this->assertSame(['operational-dashboard'], $friction->solutionPatterns()->pluck('slug')->all());
    }

    public function test_an_immediate_dry_run_is_idempotent(): void
    {
        $this->seed(LookupReferenceSeeder::class);
        $this->artisan('garcia:refresh-atlas-content')->assertSuccessful();

        $this->artisan('garcia:refresh-atlas-content', ['--dry-run' => true])
            ->expectsOutputToContain('Workflows created: 0')
            ->expectsOutputToContain('Workflows updated: 0')
            ->expectsOutputToContain('Friction points created: 0')
            ->expectsOutputToContain('Friction points updated: 0')
            ->expectsOutputToContain('Relationships changed: 0')
            ->expectsOutputToContain('Managed content unchanged: 36')
            ->expectsOutputToContain('No database changes were made.')
            ->assertSuccessful();
    }

    public function test_dry_run_is_completely_write_free(): void
    {
        $this->seed(LookupReferenceSeeder::class);
        $before = $this->databaseSnapshot();

        $this->artisan('garcia:refresh-atlas-content', ['--dry-run' => true])
            ->expectsOutputToContain('Workflows created: 18')
            ->expectsOutputToContain('Friction points created: 18')
            ->assertSuccessful();

        $this->assertSame($before, $this->databaseSnapshot());
    }

    public function test_unowned_workflow_collision_is_not_claimed_and_blocks_its_friction_point(): void
    {
        $this->seed(LookupReferenceSeeder::class);
        $legacy = Workflow::create([
            'industry_id' => Industry::where('slug', 'restaurants')->value('id'),
            'name' => 'Legacy closeout',
            'slug' => 'location-closeout-review',
            'description' => 'Production-owned workflow',
        ]);

        $this->artisan('garcia:refresh-atlas-content')->assertSuccessful();

        $this->assertNull($legacy->fresh()->managed_content_key);
        $this->assertSame('Production-owned workflow', $legacy->fresh()->description);
        $this->assertFalse(FrictionPoint::where('slug', 'restaurant-closeout-reporting')->exists());
    }

    public function test_unowned_friction_point_collision_is_not_claimed(): void
    {
        $this->seed(LookupReferenceSeeder::class);
        $legacy = FrictionPoint::create([
            'name' => 'Legacy reporting',
            'slug' => 'restaurant-closeout-reporting',
            'description' => 'Production-owned friction',
        ]);

        $this->artisan('garcia:refresh-atlas-content')->assertSuccessful();

        $this->assertNull($legacy->fresh()->managed_content_key);
        $this->assertNull($legacy->fresh()->workflow_id);
        $this->assertSame('Production-owned friction', $legacy->fresh()->description);
    }

    public function test_manually_customized_managed_workflow_is_skipped(): void
    {
        $this->seed(LookupReferenceSeeder::class);
        $this->artisan('garcia:refresh-atlas-content')->assertSuccessful();
        $workflow = Workflow::where('slug', 'location-closeout-review')->firstOrFail();
        $workflow->update(['description' => 'Administrator workflow customization']);

        $this->artisan('garcia:refresh-atlas-content')
            ->expectsOutputToContain('Protected/customized records skipped: 2')
            ->assertSuccessful();

        $this->assertSame('Administrator workflow customization', $workflow->fresh()->description);
    }

    public function test_unmodified_managed_records_can_receive_a_new_canonical_revision(): void
    {
        $this->seed(LookupReferenceSeeder::class);
        $this->artisan('garcia:refresh-atlas-content')->assertSuccessful();
        $workflow = Workflow::where('slug', 'location-closeout-review')->firstOrFail();
        $friction = FrictionPoint::where('slug', 'restaurant-closeout-reporting')->firstOrFail();

        $workflow->update(['description' => 'Previously managed workflow copy']);
        $workflow->forceFill(['managed_content_hash' => $this->managedHash($workflow, ['industry_id', 'company_type_id', 'department_id', 'name', 'description'])])->save();
        $friction->update(['description' => 'Previously managed friction copy']);
        $friction->forceFill(['managed_content_hash' => $this->managedHash($friction, ['workflow_id', 'name', 'description', 'impact'])])->save();

        $this->artisan('garcia:refresh-atlas-content')
            ->expectsOutputToContain('Workflows updated: 1')
            ->expectsOutputToContain('Friction points updated: 1')
            ->assertSuccessful();

        $this->assertStringStartsWith('An Applied Systems Lab case study', $workflow->fresh()->description);
        $this->assertSame('Manual reporting creates delays, rework, and limited visibility for the team.', $friction->fresh()->description);
    }

    public function test_manually_customized_managed_friction_point_and_its_relationships_are_skipped(): void
    {
        $this->seed(LookupReferenceSeeder::class);
        $this->artisan('garcia:refresh-atlas-content')->assertSuccessful();
        $friction = FrictionPoint::where('slug', 'restaurant-closeout-reporting')->firstOrFail();
        $patternIds = $friction->solutionPatterns()->pluck('solution_patterns.id')->all();
        $friction->update(['impact' => 'Administrator friction customization']);

        $this->artisan('garcia:refresh-atlas-content')->assertSuccessful();

        $this->assertSame('Administrator friction customization', $friction->fresh()->impact);
        $this->assertSame($patternIds, $friction->fresh()->solutionPatterns()->pluck('solution_patterns.id')->all());
    }

    public function test_articles_and_videos_are_never_modified(): void
    {
        $article = Article::create(['title' => 'Production article', 'slug' => 'production-article', 'excerpt' => 'Keep', 'body' => 'Keep body', 'is_published' => false]);
        $video = Video::create(['title' => 'Production video', 'slug' => 'production-video', 'url' => 'https://example.com/keep', 'description' => 'Keep description', 'is_published' => false]);
        $this->seed(LookupReferenceSeeder::class);

        $this->artisan('garcia:refresh-atlas-content')->assertSuccessful();

        $this->assertSame('Keep body', $article->fresh()->body);
        $this->assertFalse($article->fresh()->is_published);
        $this->assertSame('https://example.com/keep', $video->fresh()->url);
        $this->assertFalse($video->fresh()->is_published);
    }

    public function test_command_executes_no_destructive_sql(): void
    {
        $this->seed(LookupReferenceSeeder::class);
        $statements = [];
        DB::listen(function ($query) use (&$statements): void {
            $statements[] = strtolower($query->sql);
        });

        $this->artisan('garcia:refresh-atlas-content')->assertSuccessful();

        $destructive = array_filter($statements, fn (string $sql): bool => preg_match('/\b(delete|truncate|drop)\b/', $sql) === 1);
        $this->assertSame([], array_values($destructive));
    }

    public function test_starter_seeder_still_produces_all_canonical_atlas_examples(): void
    {
        $this->seed(StarterPublicContentSeeder::class);

        $this->assertSame(18, Workflow::whereNotNull('managed_content_key')->count());
        $this->assertSame(18, FrictionPoint::whereNotNull('managed_content_key')->count());
        $this->assertSame(18, DB::table('friction_point_solution_pattern')->count());
        $this->assertTrue(Workflow::where('slug', 'client-intake-and-qualification')->exists());
    }

    private function databaseSnapshot(): array
    {
        return [
            'workflows' => DB::table('workflows')->orderBy('id')->get()->map(fn ($row) => (array) $row)->all(),
            'friction_points' => DB::table('friction_points')->orderBy('id')->get()->map(fn ($row) => (array) $row)->all(),
            'relationships' => DB::table('friction_point_solution_pattern')->orderBy('id')->get()->map(fn ($row) => (array) $row)->all(),
        ];
    }

    private function managedHash($model, array $attributes): string
    {
        return hash('sha256', json_encode($model->only($attributes), JSON_THROW_ON_ERROR));
    }
}

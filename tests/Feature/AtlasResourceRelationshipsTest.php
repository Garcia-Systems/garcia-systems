<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Department;
use App\Models\Industry;
use App\Models\Service;
use App\Models\Video;
use App\Models\Workflow;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AtlasResourceRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_workflow_resource_relationships_exist(): void
    {
        $workflow = $this->workflow('inspection-review');
        $article = $this->article('Inspection Checklist');
        $video = Video::create(['title' => 'Inspection Walkthrough', 'slug' => 'inspection-walkthrough', 'url' => 'https://example.com/video', 'description' => 'Inspection workflow video.', 'is_published' => true]);
        $service = Service::create(['name' => 'Workflow Modernization', 'slug' => 'workflow-modernization', 'description' => 'Map and improve workflow handoffs.']);

        $workflow->articles()->attach($article);
        $workflow->videos()->attach($video);
        $workflow->services()->attach($service);

        $this->assertTrue($workflow->articles()->whereKey($article->id)->exists());
        $this->assertTrue($workflow->videos()->whereKey($video->id)->exists());
        $this->assertTrue($workflow->services()->whereKey($service->id)->exists());
    }

    public function test_atlas_cards_show_contextual_resource_counts(): void
    {
        $workflow = $this->workflow('inspection-review');
        $otherWorkflow = $this->workflow('unrelated-review');

        $workflow->articles()->attach($this->article('Inspection Checklist'));
        $workflow->videos()->attach(Video::create(['title' => 'Inspection Walkthrough', 'slug' => 'inspection-walkthrough', 'url' => 'https://example.com/video', 'description' => 'Inspection workflow video.', 'is_published' => true]));
        $workflow->services()->attach(Service::create(['name' => 'Workflow Modernization', 'slug' => 'workflow-modernization', 'description' => 'Map and improve workflow handoffs.']));
        $otherWorkflow->articles()->attach($this->article('Unrelated Playbook'));

        $this->get(route('atlas'))
            ->assertOk()
            ->assertSee('Inspection Checklist')
            ->assertSee('Related Articles (1)')
            ->assertSee('Related Videos (1)')
            ->assertSee('Related Services (1)')
            ->assertDontSee('Related Articles (2)');
    }

    public function test_atlas_detail_page_shows_contextual_resources_and_ctas(): void
    {
        $workflow = $this->workflow('inspection-review');
        $workflow->articles()->attach($this->article('Inspection Checklist'));
        $workflow->videos()->attach(Video::create(['title' => 'Inspection Walkthrough', 'slug' => 'inspection-walkthrough', 'url' => 'https://example.com/video', 'description' => 'Inspection workflow video.', 'is_published' => true]));
        $workflow->services()->attach(Service::create(['name' => 'Workflow Modernization', 'slug' => 'workflow-modernization', 'description' => 'Map and improve workflow handoffs.']));

        $this->get(route('atlas.workflows.show', $workflow->slug))
            ->assertOk()
            ->assertSee('Recommended articles')
            ->assertSee('Inspection Checklist')
            ->assertSee('Recommended videos')
            ->assertSee('Inspection Walkthrough')
            ->assertSee('Suggested services')
            ->assertSee('Workflow Modernization')
            ->assertSee('Suggested assessment path')
            ->assertSee('Assess readiness')
            ->assertSee('Contact Garcia Systems');
    }

    public function test_database_seeder_connects_atlas_resources(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThan(0, Workflow::has('articles')->count());
        $this->assertGreaterThan(0, Workflow::has('videos')->count());
        $this->assertGreaterThan(0, Workflow::has('services')->count());
        $this->assertDatabaseHas('industries', ['name' => 'Manufacturing']);
        $this->assertDatabaseHas('industries', ['name' => 'Healthcare']);
        $this->assertDatabaseHas('industries', ['name' => 'Professional Services']);
        $this->assertDatabaseHas('industries', ['name' => 'Logistics']);
        $this->assertDatabaseHas('industries', ['name' => 'Public Sector']);
    }

    private function workflow(string $slug): Workflow
    {
        $industry = Industry::create(['name' => str($slug)->headline().' Industry', 'slug' => $slug.'-industry', 'description' => 'Industry context.']);
        $department = Department::create(['name' => str($slug)->headline().' Department', 'slug' => $slug.'-department', 'description' => 'Department context.']);

        return Workflow::create([
            'industry_id' => $industry->id,
            'department_id' => $department->id,
            'name' => str($slug)->headline(),
            'slug' => $slug,
            'description' => 'A workflow with contextual resources.',
            'assessment_path' => 'Review workflow documentation and data readiness.',
        ]);
    }

    private function article(string $title): Article
    {
        $category = Category::firstOrCreate(['slug' => 'operations'], ['name' => 'Operations', 'description' => 'Operations content.']);

        return Article::create([
            'category_id' => $category->id,
            'title' => $title,
            'slug' => str($title)->slug(),
            'excerpt' => 'Practical guidance.',
            'body' => 'Detailed practical guidance.',
            'published_at' => now()->subDay(),
        ]);
    }
}

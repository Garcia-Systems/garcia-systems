<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaboratoriesContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_curated_featured_laboratories(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Executable systems, not static case studies.')
            ->assertSee('Inventory Synchronization Laboratory')
            ->assertSee('Executable Digital Banking Integration Laboratory')
            ->assertSee('Executable Regional Economy Laboratory')
            ->assertSee('Executable Sales Engineering Laboratory')
            ->assertSee('Ledger')
            ->assertSee('Member experience')
            ->assertSee('Ending positions')
            ->assertSee('Recommendation');
    }

    public function test_laboratory_page_explains_the_method_and_renders_catalog(): void
    {
        $this->get('/laboratories')
            ->assertOk()
            ->assertSee('Read the concept. Run the model. Inspect the behavior. Change the assumptions. Verify the result.')
            ->assertSee('narrative chapters')
            ->assertSee('automated tests')
            ->assertSee('not presented as production reference architectures')
            ->assertSee('https://github.com/Garcia-Systems/inventory-synchronization-laboratory', false);
    }

    public function test_laboratory_metadata_has_required_fields_and_featured_entries(): void
    {
        $laboratories = config('laboratories.items');
        $this->assertNotEmpty($laboratories);
        $this->assertCount(4, collect($laboratories)->where('featured', true));

        foreach ($laboratories as $laboratory) {
            foreach (['title', 'slug', 'description', 'domain', 'repository_url', 'status', 'technologies', 'key_concepts', 'architecture', 'featured'] as $field) {
                $this->assertArrayHasKey($field, $laboratory);
            }
        }
    }

    public function test_homepage_ai_preview_is_an_unscored_model_not_a_result(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('No score assigned')
            ->assertSee('Workflow Clarity')
            ->assertSee('Data Quality')
            ->assertSee('Risk / Governance')
            ->assertSee('Not evaluated')
            ->assertDontSee('conic-gradient')
            ->assertDontSee('readiness assessment gauge');
    }

    public function test_empty_articles_and_videos_do_not_create_empty_homepage_sections(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('Articles are coming soon.')
            ->assertDontSee('Video library is coming soon.')
            ->assertDontSee('Supporting knowledge');
    }
}

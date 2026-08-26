<?php

namespace Tests\Feature;

use App\Models\{Article, Video};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPositioningTest extends TestCase
{
    use RefreshDatabase;

    public function test_core_pages_present_business_first_solutions_engineering_positioning(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Business-first systems consulting')
            ->assertSee('what—if anything—is worth implementing', false)
            ->assertSee('Specialized assessment tool');

        $this->get(route('services'))
            ->assertOk()
            ->assertSee('Business-first Solutions Engineering')
            ->assertSee('buy or configure a product')
            ->assertSee('do nothing');

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('focused independent practice')
            ->assertSee('Workflow &amp; Systems Analysis', false);

        $this->get(route('atlas'))
            ->assertOk()
            ->assertSee('Applied Systems Labs')
            ->assertSee('not claims of paid client experience');
    }

    public function test_assessment_and_published_content_routes_remain_available(): void
    {
        config(['garcia.features.ai_assessment' => true]);
        $article = Article::create(['title' => 'Historical article', 'slug' => 'historical-article', 'body' => 'Historical copy', 'is_published' => true, 'published_at' => now()]);
        Video::create(['title' => 'Existing video', 'slug' => 'existing-video', 'url' => 'https://example.com/video', 'is_published' => true]);

        $this->get(route('assessment'))->assertOk();
        $this->get(route('articles.show', $article))->assertOk()->assertSee('Historical copy');
        $this->get(route('videos'))->assertOk()->assertSee('https://example.com/video', false);
    }
}

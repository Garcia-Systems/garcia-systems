<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMetadataTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_pages_render_metadata_and_article_schema(): void
    {
        $article = Article::create([
            'title' => 'Workflow Modernization Checklist',
            'slug' => 'workflow-modernization-checklist',
            'seo_title' => 'Workflow Modernization SEO Title',
            'seo_description' => 'A practical SEO description for workflow modernization leaders.',
            'featured_image_url' => 'https://example.com/workflow.jpg',
            'excerpt' => 'A fallback excerpt.',
            'body' => 'A longer body for fallback descriptions.',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $this->get(route('articles.show', $article))
            ->assertOk()
            ->assertSee('<meta name="description" content="A practical SEO description for workflow modernization leaders.">', false)
            ->assertSee('<meta property="og:title" content="Workflow Modernization SEO Title">', false)
            ->assertSee('<link rel="canonical" href="'.route('articles.show', $article).'">', false)
            ->assertSee('"@type":"Article"', false)
            ->assertSee('"headline":"Workflow Modernization SEO Title"', false);
    }

    public function test_article_pages_fall_back_to_excerpt_when_seo_description_is_missing(): void
    {
        $article = Article::create([
            'title' => 'Fallback Metadata Article',
            'slug' => 'fallback-metadata-article',
            'excerpt' => 'This excerpt becomes the metadata description.',
            'body' => 'The article body is available if the excerpt is missing.',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $this->get(route('articles.show', $article))
            ->assertOk()
            ->assertSee('<meta name="description" content="This excerpt becomes the metadata description.">', false);
    }

    public function test_homepage_contains_default_metadata(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<title>Garcia Systems</title>', false)
            ->assertSee('<meta name="description" content="Business-first systems consulting for products, systems, automation, and AI-ready workflows that teams can adopt and measure.">', false)
            ->assertSee('<meta property="og:title" content="Garcia Systems">', false)
            ->assertSee('<link rel="canonical" href="'.route('home').'">', false)
            ->assertSee('"@type":"Organization"', false)
            ->assertSee('"@type":"WebSite"', false);
    }
}

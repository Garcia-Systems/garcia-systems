<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Models\Video;
use App\Support\SubstackMetadataFetcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExternalEmbedsTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_normal_article_without_embed_still_renders(): void
    {
        $article = Article::create(['title' => 'Normal Article', 'slug' => 'normal-article', 'excerpt' => 'Excerpt', 'body' => 'Normal body', 'published_at' => now()]);
        $this->get(route('articles.show', $article))->assertOk()->assertSee('Normal body')->assertDontSee('Read on Substack');
    }

    public function test_external_substack_article_preview_does_not_require_body_or_image(): void
    {
        $article = Article::create(['title' => 'Substack Article', 'slug' => 'substack-article', 'excerpt' => 'Excerpt', 'body' => null, 'external_url' => 'https://garciasystems.substack.com/p/example', 'published_at' => now()]);

        $this->get(route('articles.show', $article))->assertOk()
            ->assertSee('Substack Article')
            ->assertSee('Read the full article on Substack')
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener noreferrer"', false)
            ->assertDontSee('substack-post-embed', false);
    }

    public function test_admin_accepts_official_substack_urls_and_extracts_open_graph_image(): void
    {
        $this->app->instance(SubstackMetadataFetcher::class, new class extends SubstackMetadataFetcher {
            public function fetch(string $url): array { return ['image' => 'https://cdn.substack.com/image.jpg', 'title' => 'OG title', 'description' => 'OG description']; }
        });

        $user = User::factory()->create();
        $this->actingAs($user)->post(route('admin.articles.store'), [
            'title' => 'External Article', 'excerpt' => 'Admin excerpt', 'body' => '',
            'external_url' => 'https://substack.com/@garciasystems/p/example', 'is_published' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('articles', [
            'slug' => 'external-article',
            'external_url' => 'https://substack.com/@garciasystems/p/example',
            'external_preview_image_url' => 'https://cdn.substack.com/image.jpg',
            'title' => 'External Article',
            'excerpt' => 'Admin excerpt',
        ]);
    }

    public function test_admin_accepts_publication_subdomains_and_rejects_unrelated_domains(): void
    {
        $this->app->instance(SubstackMetadataFetcher::class, new class extends SubstackMetadataFetcher { public function fetch(string $url): array { return []; } });
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('admin.articles.store'), [
            'title' => 'Valid Subdomain', 'excerpt' => 'Excerpt', 'external_url' => 'https://garciasystems.substack.com/p/example',
        ])->assertRedirect();

        $this->actingAs($user)->post(route('admin.articles.store'), [
            'title' => 'Bad Domain', 'excerpt' => 'Excerpt', 'external_url' => 'https://example.com/post',
        ])->assertSessionHasErrors('external_url');
    }

    public function test_metadata_failure_is_graceful_and_featured_image_takes_precedence(): void
    {
        $this->app->instance(SubstackMetadataFetcher::class, new class extends SubstackMetadataFetcher { public function fetch(string $url): array { return []; } });
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('admin.articles.store'), [
            'title' => 'Featured External', 'excerpt' => 'Excerpt', 'body' => '',
            'featured_image_url' => 'https://example.com/admin.jpg',
            'external_url' => 'https://garciasystems.substack.com/p/example', 'is_published' => '1',
        ])->assertRedirect();

        $article = Article::where('slug', 'featured-external')->firstOrFail();
        $this->assertNull($article->external_preview_image_url);
        $this->assertSame('https://example.com/admin.jpg', $article->preview_image_url);
    }

    public function test_external_article_index_card_and_unpublished_visibility(): void
    {
        Article::create(['title' => 'Shown External', 'slug' => 'shown-external', 'excerpt' => 'External excerpt', 'body' => null, 'external_url' => 'https://garciasystems.substack.com/p/shown', 'external_preview_image_url' => 'https://cdn.substack.com/shown.jpg', 'published_at' => now()]);
        Article::create(['title' => 'Hidden External', 'slug' => 'hidden-external', 'excerpt' => 'Hidden excerpt', 'body' => null, 'external_url' => 'https://garciasystems.substack.com/p/hidden', 'is_published' => false]);

        $this->get(route('articles.index'))->assertOk()
            ->assertSee('Shown External')
            ->assertSee('Read on Substack')
            ->assertSee('https://garciasystems.substack.com/p/shown', false)
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener noreferrer"', false)
            ->assertDontSee('Hidden External');

        $this->get(route('articles.show', Article::where('slug', 'hidden-external')->first()))->assertNotFound();
    }

    public function test_youtube_url_formats_are_parsed(): void
    {
        $id = 'dQw4w9WgXcQ';
        foreach (["https://www.youtube.com/watch?v={$id}", "https://youtu.be/{$id}", "https://www.youtube.com/shorts/{$id}", "https://www.youtube.com/embed/{$id}"] as $url) {
            $this->assertSame($id, Video::parseYoutubeVideoId($url));
        }
    }

    public function test_malformed_or_unrelated_video_urls_are_rejected(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('admin.videos.store'), [
            'title' => 'Bad Video', 'youtube_url' => 'https://vimeo.com/123456789', 'description' => 'Description',
        ])->assertSessionHasErrors('youtube_url');
    }

    public function test_videos_render_responsive_accessible_iframe_and_fallback(): void
    {
        Video::create(['title' => 'Workflow Video', 'slug' => 'workflow-video', 'url' => 'https://youtu.be/dQw4w9WgXcQ', 'description' => 'Description']);
        $this->get(route('videos'))->assertOk()
            ->assertSee('video-embed', false)
            ->assertSee('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', false)
            ->assertSee('title="Workflow Video video player"', false)
            ->assertSee('loading="lazy"', false)
            ->assertSee('allowfullscreen', false)
            ->assertSee('Watch on YouTube');
    }

    public function test_published_and_unpublished_video_behavior_is_preserved(): void
    {
        Video::create(['title' => 'Shown Video', 'slug' => 'shown-video', 'url' => 'https://youtu.be/dQw4w9WgXcQ', 'description' => 'Shown']);
        Video::create(['title' => 'Hidden Video', 'slug' => 'hidden-video', 'url' => 'https://youtu.be/9bZkp7q19f0', 'description' => 'Hidden', 'is_published' => false]);
        $this->get(route('videos'))->assertOk()->assertSee('Shown Video')->assertDontSee('Hidden Video');
    }
}

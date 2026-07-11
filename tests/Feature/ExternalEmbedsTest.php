<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Models\Video;
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

    public function test_article_with_valid_substack_embed_renders_and_does_not_require_body_or_image(): void
    {
        $embed = '<div class="substack-post-embed"><p>Official note</p><a href="https://garciasystems.substack.com/p/example">Read</a></div><script async src="https://substack.com/embedjs/embed.js" charset="utf-8"></script>';
        $article = Article::create(['title' => 'Substack Article', 'slug' => 'substack-article', 'excerpt' => 'Excerpt', 'body' => null, 'substack_embed_code' => $embed, 'published_at' => now()]);

        $this->get(route('articles.show', $article))->assertOk()
            ->assertSee('Substack Article')
            ->assertSee('substack-post-embed', false)
            ->assertSee('https://garciasystems.substack.com/p/example', false)
            ->assertSee('Read on Substack')
            ->assertSee('https://substack.com/embedjs/embed.js', false);
    }

    public function test_admin_rejects_malicious_or_unrelated_substack_embed(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('admin.articles.store'), [
            'title' => 'Bad Embed', 'excerpt' => 'Excerpt', 'body' => '',
            'substack_embed_code' => '<iframe src="https://evil.example/embed"></iframe><a href="https://evil.example/post">bad</a>',
        ]);

        $response->assertSessionHasErrors('substack_embed_code');
        $this->assertDatabaseMissing('articles', ['slug' => 'bad-embed']);
    }

    public function test_unpublished_embedded_articles_remain_hidden(): void
    {
        $article = Article::create(['title' => 'Hidden Embed', 'slug' => 'hidden-embed', 'excerpt' => 'Excerpt', 'body' => null, 'substack_embed_code' => '<div class="substack-post-embed"><a href="https://garciasystems.substack.com/p/hidden">Read</a></div>', 'is_published' => false]);
        $this->get(route('articles.show', $article))->assertNotFound();
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

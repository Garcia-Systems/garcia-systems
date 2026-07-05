<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_cannot_access_admin(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_admin(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertOk()
            ->assertSee('Garcia Systems CMS');
    }

    public function test_article_crud_works(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Strategy', 'slug' => 'strategy']);
        $tag = Tag::create(['name' => 'Automation', 'slug' => 'automation']);

        $response = $this->actingAs($user)->post('/admin/articles', [
            'title' => 'CMS Article', 'slug' => '', 'seo_title' => 'SEO CMS Article', 'seo_description' => 'Search summary',
            'category_id' => $category->id, 'tag_ids' => [$tag->id], 'featured_image_url' => 'https://example.com/image.jpg',
            'excerpt' => 'Short summary', 'body' => 'Long article body', 'is_published' => '1',
        ]);

        $article = Article::where('slug', 'cms-article')->firstOrFail();
        $response->assertRedirect(route('admin.articles.edit', $article));
        $this->assertTrue($article->tags()->whereKey($tag->id)->exists());

        $this->actingAs($user)->put(route('admin.articles.update', $article), [
            'title' => 'Updated CMS Article', 'slug' => 'updated-cms-article', 'seo_title' => 'Updated SEO',
            'seo_description' => 'Updated search summary', 'category_id' => $category->id, 'tag_ids' => [$tag->id],
            'featured_image_url' => 'https://example.com/updated.jpg', 'excerpt' => 'Updated summary', 'body' => 'Updated body',
        ])->assertRedirect();

        $this->assertDatabaseHas('articles', ['id' => $article->id, 'title' => 'Updated CMS Article', 'slug' => 'updated-cms-article', 'is_published' => false]);
    }

    public function test_video_crud_works(): void
    {
        $user = User::factory()->create();
        $article = Article::create(['title' => 'Related', 'slug' => 'related', 'excerpt' => 'Excerpt', 'body' => 'Body']);

        $response = $this->actingAs($user)->post('/admin/videos', [
            'title' => 'CMS Video', 'slug' => '', 'youtube_url' => 'https://www.youtube.com/watch?v=abc123',
            'description' => 'Video description', 'transcript' => 'Video transcript', 'article_id' => $article->id, 'is_published' => '1',
        ]);

        $video = Video::where('slug', 'cms-video')->firstOrFail();
        $response->assertRedirect(route('admin.videos.edit', $video));

        $this->actingAs($user)->put(route('admin.videos.update', $video), [
            'title' => 'Updated CMS Video', 'slug' => 'updated-cms-video', 'youtube_url' => 'https://youtu.be/xyz987',
            'description' => 'Updated description', 'transcript' => 'Updated transcript', 'article_id' => $article->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('videos', ['id' => $video->id, 'title' => 'Updated CMS Video', 'slug' => 'updated-cms-video', 'is_published' => false]);
    }

    public function test_publish_and_unpublish_works(): void
    {
        $user = User::factory()->create();
        $article = Article::create(['title' => 'Draft', 'slug' => 'draft', 'excerpt' => 'Excerpt', 'body' => 'Body', 'is_published' => false]);
        $video = Video::create(['title' => 'Draft Video', 'slug' => 'draft-video', 'url' => 'https://youtu.be/abc', 'description' => 'Description', 'is_published' => false]);

        $this->actingAs($user)->patch(route('admin.articles.publish', $article))->assertRedirect();
        $this->assertTrue($article->fresh()->is_published);
        $this->assertNotNull($article->fresh()->published_at);
        $this->actingAs($user)->patch(route('admin.articles.publish', $article))->assertRedirect();
        $this->assertFalse($article->fresh()->is_published);

        $this->actingAs($user)->patch(route('admin.videos.publish', $video))->assertRedirect();
        $this->assertTrue($video->fresh()->is_published);
        $this->actingAs($user)->patch(route('admin.videos.publish', $video))->assertRedirect();
        $this->assertFalse($video->fresh()->is_published);
    }
}

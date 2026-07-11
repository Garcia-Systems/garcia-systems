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
            'title' => 'CMS Video', 'slug' => '', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'description' => 'Video description', 'transcript' => 'Video transcript', 'article_id' => $article->id, 'is_published' => '1',
        ]);

        $video = Video::where('slug', 'cms-video')->firstOrFail();
        $response->assertRedirect(route('admin.videos.edit', $video));

        $this->actingAs($user)->put(route('admin.videos.update', $video), [
            'title' => 'Updated CMS Video', 'slug' => 'updated-cms-video', 'youtube_url' => 'https://youtu.be/9bZkp7q19f0',
            'description' => 'Updated description', 'transcript' => 'Updated transcript', 'article_id' => $article->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('videos', ['id' => $video->id, 'title' => 'Updated CMS Video', 'slug' => 'updated-cms-video', 'is_published' => false]);
    }

    public function test_publish_and_unpublish_works(): void
    {
        $user = User::factory()->create();
        $article = Article::create(['title' => 'Draft', 'slug' => 'draft', 'excerpt' => 'Excerpt', 'body' => 'Body', 'is_published' => false]);
        $video = Video::create(['title' => 'Draft Video', 'slug' => 'draft-video', 'url' => 'https://youtu.be/dQw4w9WgXcQ', 'description' => 'Description', 'is_published' => false]);

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

    public function test_admin_dashboard_displays_metrics_and_recent_activity(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Ops', 'slug' => 'ops']);
        $article = Article::create(['category_id' => $category->id, 'title' => 'Recent Ops Article', 'slug' => 'recent-ops-article', 'excerpt' => 'Excerpt', 'body' => 'Body']);
        Video::create(['title' => 'Recent Video', 'slug' => 'recent-video', 'url' => 'https://example.com/video', 'description' => 'Description']);
        \App\Models\Industry::create(['name' => 'Manufacturing', 'slug' => 'manufacturing']);
        \App\Models\Assessment::create(['email' => 'ops@example.com', 'score' => 12]);
        \App\Models\ContactSubmission::create(['name' => 'Alex Admin', 'email' => 'alex@example.com', 'message' => 'Hello']);

        $this->actingAs($user)->get('/admin')
            ->assertOk()
            ->assertSee('Articles')
            ->assertSee('Videos')
            ->assertSee('Industries')
            ->assertSee('Assessments')
            ->assertSee('Contact submissions')
            ->assertSee('Recent articles')
            ->assertSee($article->title)
            ->assertSee('Recent assessments')
            ->assertSee('ops@example.com')
            ->assertSee('Recent contact submissions')
            ->assertSee('Alex Admin');
    }

    public function test_article_admin_search_matches_expected_fields(): void
    {
        $user = User::factory()->create();
        Article::create(['title' => 'Workflow Search Match', 'slug' => 'workflow-search-match', 'excerpt' => 'Plain excerpt', 'body' => 'Plain body']);
        Article::create(['title' => 'Other Article', 'slug' => 'other-article', 'excerpt' => 'Needle excerpt', 'body' => 'Plain body']);
        Article::create(['title' => 'Hidden Body', 'slug' => 'hidden-body', 'excerpt' => 'Plain excerpt', 'body' => 'Needle body']);
        Article::create(['title' => 'Unrelated', 'slug' => 'unrelated', 'excerpt' => 'Plain excerpt', 'body' => 'Plain body']);

        $this->actingAs($user)->get(route('admin.articles.index', ['search' => 'Needle']))
            ->assertOk()
            ->assertSee('Other Article')
            ->assertSee('Hidden Body')
            ->assertDontSee('Workflow Search Match')
            ->assertDontSee('Unrelated');
    }

    public function test_video_admin_search_matches_expected_fields(): void
    {
        $user = User::factory()->create();
        Video::create(['title' => 'Video Title Match', 'slug' => 'video-title-match', 'url' => 'https://example.com/1', 'description' => 'Plain description']);
        Video::create(['title' => 'Description Video', 'slug' => 'description-video', 'url' => 'https://example.com/2', 'description' => 'Needle description']);
        Video::create(['title' => 'Transcript Video', 'slug' => 'transcript-video', 'url' => 'https://example.com/3', 'description' => 'Plain description', 'transcript' => 'Needle transcript']);
        Video::create(['title' => 'Unrelated Video', 'slug' => 'unrelated-video', 'url' => 'https://example.com/4', 'description' => 'Plain description']);

        $this->actingAs($user)->get(route('admin.videos.index', ['search' => 'Needle']))
            ->assertOk()
            ->assertSee('Description Video')
            ->assertSee('Transcript Video')
            ->assertDontSee('Video Title Match')
            ->assertDontSee('Unrelated Video');
    }

    public function test_category_crud_and_safe_delete_behavior(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('admin.categories.store'), [
            'name' => 'Modernization',
            'slug' => '',
            'description' => 'Workflow modernization topics.',
        ])->assertRedirect();

        $category = Category::where('slug', 'modernization')->firstOrFail();

        $this->actingAs($user)->put(route('admin.categories.update', $category), [
            'name' => 'Workflow Modernization',
            'slug' => 'workflow-modernization',
            'description' => 'Updated description.',
        ])->assertRedirect(route('admin.categories.index'));

        $category = $category->fresh();
        $this->assertSame('Workflow Modernization', $category->name);

        Article::create(['category_id' => $category->id, 'title' => 'Related Article', 'slug' => 'related-article', 'excerpt' => 'Excerpt', 'body' => 'Body']);
        $this->actingAs($user)->delete(route('admin.categories.destroy', $category))->assertRedirect();
        $this->assertDatabaseHas('categories', ['id' => $category->id]);

        Article::query()->delete();
        $this->actingAs($user)->delete(route('admin.categories.destroy', $category))->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_tag_crud_and_safe_delete_behavior(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('admin.tags.store'), ['name' => 'Automation', 'slug' => ''])->assertRedirect();
        $tag = Tag::where('slug', 'automation')->firstOrFail();

        $this->actingAs($user)->put(route('admin.tags.update', $tag), ['name' => 'Practical Automation', 'slug' => 'practical-automation'])->assertRedirect(route('admin.tags.index'));
        $tag = $tag->fresh();
        $this->assertSame('Practical Automation', $tag->name);

        $article = Article::create(['title' => 'Tagged Article', 'slug' => 'tagged-article', 'excerpt' => 'Excerpt', 'body' => 'Body']);
        $article->tags()->attach($tag);
        $this->actingAs($user)->delete(route('admin.tags.destroy', $tag))->assertRedirect();
        $this->assertDatabaseHas('tags', ['id' => $tag->id]);

        $article->tags()->detach($tag);
        $this->actingAs($user)->delete(route('admin.tags.destroy', $tag))->assertRedirect(route('admin.tags.index'));
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    public function test_admin_can_archive_article_and_restore_it(): void
    {
        $user = User::factory()->create();
        $article = Article::create(['title' => 'Archive Me', 'slug' => 'archive-me', 'excerpt' => 'Excerpt', 'body' => 'Body', 'is_published' => true, 'published_at' => now()]);

        $this->actingAs($user)->delete(route('admin.articles.destroy', $article))
            ->assertRedirect(route('admin.articles.index'))
            ->assertSessionHas('status', 'Article archived.');

        $this->assertSoftDeleted('articles', ['id' => $article->id]);
        $this->get(route('articles.index'))->assertDontSee('Archive Me');
        $this->get(route('articles.show', $article))->assertNotFound();

        $this->actingAs($user)->get(route('admin.articles.index', ['status' => 'archived']))
            ->assertOk()
            ->assertSee('Archive Me')
            ->assertSee('Restore');

        $this->actingAs($user)->patch(route('admin.articles.restore', $article->id))
            ->assertRedirect(route('admin.articles.edit', $article))
            ->assertSessionHas('status', 'Article restored.');

        $this->assertFalse($article->fresh()->trashed());
        $this->get(route('articles.index'))->assertSee('Archive Me');
    }

    public function test_admin_can_archive_video_and_restore_it(): void
    {
        $user = User::factory()->create();
        $video = Video::create(['title' => 'Archive Video', 'slug' => 'archive-video', 'url' => 'https://example.com/video', 'description' => 'Description', 'is_published' => true]);

        $this->actingAs($user)->delete(route('admin.videos.destroy', $video))
            ->assertRedirect(route('admin.videos.index'))
            ->assertSessionHas('status', 'Video archived.');

        $this->assertSoftDeleted('videos', ['id' => $video->id]);
        $this->get(route('videos'))->assertDontSee('Archive Video');

        $this->actingAs($user)->get(route('admin.videos.index', ['status' => 'archived']))
            ->assertOk()
            ->assertSee('Archive Video')
            ->assertSee('Restore');

        $this->actingAs($user)->patch(route('admin.videos.restore', $video->id))
            ->assertRedirect(route('admin.videos.edit', $video))
            ->assertSessionHas('status', 'Video restored.');

        $this->assertFalse($video->fresh()->trashed());
        $this->get(route('videos'))->assertSee('Archive Video');
    }

    public function test_unauthenticated_users_cannot_archive_or_restore_articles_and_videos(): void
    {
        $article = Article::create(['title' => 'Protected Article', 'slug' => 'protected-article', 'excerpt' => 'Excerpt', 'body' => 'Body']);
        $video = Video::create(['title' => 'Protected Video', 'slug' => 'protected-video', 'url' => 'https://example.com/video', 'description' => 'Description']);

        $this->delete(route('admin.articles.destroy', $article))->assertRedirect('/login');
        $this->patch(route('admin.articles.restore', $article->id))->assertRedirect('/login');
        $this->delete(route('admin.videos.destroy', $video))->assertRedirect('/login');
        $this->patch(route('admin.videos.restore', $video->id))->assertRedirect('/login');

        $this->assertDatabaseHas('articles', ['id' => $article->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('videos', ['id' => $video->id, 'deleted_at' => null]);
    }

}

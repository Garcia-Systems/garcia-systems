<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicArticlesExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_search_matches_title_excerpt_and_body(): void
    {
        $category = Category::create(['name' => 'Operations', 'slug' => 'operations']);

        Article::create(['category_id' => $category->id, 'title' => 'Automation Roadmap', 'slug' => 'automation-roadmap', 'excerpt' => 'Practical planning.', 'body' => 'Workflow details.', 'published_at' => now()->subDay()]);
        Article::create(['category_id' => $category->id, 'title' => 'Reporting Playbook', 'slug' => 'reporting-playbook', 'excerpt' => 'Automation appears in this excerpt.', 'body' => 'Metrics.', 'published_at' => now()->subDays(2)]);
        Article::create(['category_id' => $category->id, 'title' => 'Intake Systems', 'slug' => 'intake-systems', 'excerpt' => 'Structured requests.', 'body' => 'Body mentions automation opportunities.', 'published_at' => now()->subDays(3)]);
        Article::create(['category_id' => $category->id, 'title' => 'Unrelated Article', 'slug' => 'unrelated-article', 'excerpt' => 'No match.', 'body' => 'Nothing relevant.', 'published_at' => now()->subDays(4)]);

        $this->get('/articles?q=automation')
            ->assertOk()
            ->assertSee('3 articles found')
            ->assertSee('Automation Roadmap')
            ->assertSee('Reporting Playbook')
            ->assertSee('Intake Systems')
            ->assertDontSee('Unrelated Article');
    }

    public function test_category_filter_uses_query_string(): void
    {
        $operations = Category::create(['name' => 'Operations', 'slug' => 'operations']);
        $ai = Category::create(['name' => 'AI', 'slug' => 'ai']);

        Article::create(['category_id' => $operations->id, 'title' => 'Operations Workflow', 'slug' => 'operations-workflow', 'excerpt' => 'Ops.', 'body' => 'Ops body.', 'published_at' => now()->subDay()]);
        Article::create(['category_id' => $ai->id, 'title' => 'AI Workflow', 'slug' => 'ai-workflow', 'excerpt' => 'AI.', 'body' => 'AI body.', 'published_at' => now()->subDay()]);

        $this->get('/articles?category=Operations')
            ->assertOk()
            ->assertSee('1 article found')
            ->assertSee('Operations Workflow')
            ->assertDontSee('AI Workflow');
    }

    public function test_article_pagination_still_works_and_preserves_query_strings(): void
    {
        $category = Category::create(['name' => 'Operations', 'slug' => 'operations']);

        foreach (range(1, 10) as $number) {
            Article::create([
                'category_id' => $category->id,
                'title' => "Workflow Automation {$number}",
                'slug' => "workflow-automation-{$number}",
                'excerpt' => 'Automation pagination example.',
                'body' => 'Pagination body.',
                'published_at' => now()->subDays($number),
            ]);
        }

        $this->get('/articles?q=automation&category=operations')
            ->assertOk()
            ->assertSee('10 articles found')
            ->assertSee('page=2', false)
            ->assertSee('q=automation', false)
            ->assertSee('category=operations', false);
    }

    public function test_article_detail_renders_metadata_featured_image_and_related_articles(): void
    {
        $strategy = Category::create(['name' => 'Strategy', 'slug' => 'strategy']);
        $operations = Category::create(['name' => 'Operations', 'slug' => 'operations']);

        $article = Article::create([
            'category_id' => $strategy->id,
            'title' => 'Main Strategy Article',
            'slug' => 'main-strategy-article',
            'excerpt' => 'A styled excerpt for the article detail page.',
            'body' => str_repeat('A body word for reading time. ', 80),
            'featured_image_url' => 'https://example.com/main.jpg',
            'published_at' => now()->subDay(),
        ]);

        Article::create(['category_id' => $strategy->id, 'title' => 'Related Strategy One', 'slug' => 'related-strategy-one', 'excerpt' => 'Related one.', 'body' => 'Related body.', 'featured_image_url' => 'https://example.com/one.jpg', 'published_at' => now()->subDays(2)]);
        Article::create(['category_id' => $strategy->id, 'title' => 'Related Strategy Two', 'slug' => 'related-strategy-two', 'excerpt' => 'Related two.', 'body' => 'Related body.', 'published_at' => now()->subDays(3)]);
        Article::create(['category_id' => $operations->id, 'title' => 'Fallback Recent Article', 'slug' => 'fallback-recent-article', 'excerpt' => 'Fallback.', 'body' => 'Fallback body.', 'published_at' => now()->subDays(4)]);

        $this->get(route('articles.show', $article))
            ->assertOk()
            ->assertSee('https://example.com/main.jpg', false)
            ->assertSee('Strategy')
            ->assertSee($article->published_at->format('F j, Y'))
            ->assertSee('By Garcia Systems')
            ->assertSee('min read')
            ->assertSee('Need help implementing ideas like these?')
            ->assertSee('Related Strategy One')
            ->assertSee('Related Strategy Two')
            ->assertSee('Fallback Recent Article');
    }

    public function test_featured_images_display_on_article_index(): void
    {
        Article::create([
            'title' => 'Image Article',
            'slug' => 'image-article',
            'excerpt' => 'Featured image excerpt.',
            'body' => 'Featured image body.',
            'featured_image_url' => 'https://example.com/featured.jpg',
            'published_at' => now()->subDay(),
        ]);

        $this->get('/articles')
            ->assertOk()
            ->assertSee('https://example.com/featured.jpg', false)
            ->assertSee('Featured image for Image Article');
    }
}

<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Database\Seeders\BlogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_blog_endpoint_returns_published_posts_with_filters(): void
    {
        $this->seed(BlogSeeder::class);

        $this->getJson('/api/v1/news')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'kuala-lumpur-southeast-asia-concert-capital')
            ->assertJsonPath('data.0.category.slug', 'concert-intelligence')
            ->assertJsonPath('meta.per_page', 9)
            ->assertJsonPath('meta.filters.categories.0.slug', 'concert-intelligence')
            ->assertJsonPath('meta.filters.tags.0.slug', 'kuala-lumpur');
    }

    public function test_public_blog_endpoint_filters_by_category_tag_and_search(): void
    {
        $this->seed(BlogSeeder::class);

        $this->getJson('/api/v1/news?category=behind-the-scenes')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'behind-the-build-of-a-festival-stage');

        $this->getJson('/api/v1/news?tag=ticketing')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'what-fans-should-check-before-buying-concert-tickets');

        $this->getJson('/api/v1/news?search=durable')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'why-artist-stories-matter-before-show-day');
    }

    public function test_public_blog_detail_endpoint_returns_article_payload(): void
    {
        $this->seed(BlogSeeder::class);

        $this->getJson('/api/v1/news/kuala-lumpur-southeast-asia-concert-capital')
            ->assertOk()
            ->assertJsonPath('data.title', 'How Kuala Lumpur Became Southeast Asia Concert Capital')
            ->assertJsonPath('data.author.name', 'Black Sky Editorial')
            ->assertJsonPath('data.canonical_url', url('/news/kuala-lumpur-southeast-asia-concert-capital'));
    }

    public function test_public_blog_detail_sanitizes_rich_article_html(): void
    {
        $this->seed(BlogSeeder::class);

        BlogPost::query()
            ->where('slug', 'kuala-lumpur-southeast-asia-concert-capital')
            ->firstOrFail()
            ->update([
                'content' => '<p>Safe update</p><script>alert("xss")</script><a href="javascript:alert(1)" onclick="alert(2)">Bad link</a><a href="https://blacksky.test">Good link</a>',
            ]);

        $content = (string) $this->getJson('/api/v1/news/kuala-lumpur-southeast-asia-concert-capital')
            ->assertOk()
            ->json('data.content');

        $this->assertStringContainsString('<p>Safe update</p>', $content);
        $this->assertStringContainsString('https://blacksky.test', $content);
        $this->assertStringNotContainsString('<script', $content);
        $this->assertStringNotContainsString('javascript:', $content);
        $this->assertStringNotContainsString('onclick', $content);
    }

    public function test_blog_pages_have_indexable_meta(): void
    {
        $this->withoutVite();
        $this->seed(BlogSeeder::class);

        $this->get('/news')
            ->assertOk()
            ->assertSee('News | Black Sky Enterprise')
            ->assertSee('CollectionPage');

        $this->get('/news/kuala-lumpur-southeast-asia-concert-capital')
            ->assertOk()
            ->assertSee('How Kuala Lumpur Became Southeast Asia Concert Capital | Black Sky News')
            ->assertSee('Article');
    }
}

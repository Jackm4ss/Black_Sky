<?php

namespace Tests\Feature;

use App\Models\PortfolioWork;
use Database\Seeders\PortfolioWorkSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPortfolioTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_portfolio_endpoint_returns_published_works(): void
    {
        $this->seed(PortfolioWorkSeeder::class);

        $this->getJson('/api/v1/portfolio')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'neon-pulse')
            ->assertJsonPath('data.0.category', 'Arena Concert')
            ->assertJsonPath('meta.per_page', 8)
            ->assertJsonPath('meta.filters.categories.0', 'Arena Concert');
    }

    public function test_public_portfolio_endpoint_supports_search_and_detail(): void
    {
        $this->seed(PortfolioWorkSeeder::class);

        $this->getJson('/api/v1/portfolio?search=tidal')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'tidal-wave');

        $this->getJson('/api/v1/portfolio/neon-pulse')
            ->assertOk()
            ->assertJsonPath('data.title', 'Neon Pulse')
            ->assertJsonPath('data.canonical_url', url('/portfolio/neon-pulse'));
    }

    public function test_public_portfolio_detail_sanitizes_rich_description_html(): void
    {
        $this->seed(PortfolioWorkSeeder::class);

        PortfolioWork::query()
            ->where('slug', 'neon-pulse')
            ->firstOrFail()
            ->update([
                'description' => '<h2>Safe project</h2><iframe src="https://example.com"></iframe><a href="javascript:alert(1)" onmouseover="alert(2)">Bad link</a>',
            ]);

        $description = (string) $this->getJson('/api/v1/portfolio/neon-pulse')
            ->assertOk()
            ->json('data.description');

        $this->assertStringContainsString('<h2>Safe project</h2>', $description);
        $this->assertStringNotContainsString('<iframe', $description);
        $this->assertStringNotContainsString('javascript:', $description);
        $this->assertStringNotContainsString('onmouseover', $description);
    }

    public function test_portfolio_detail_page_has_indexable_meta(): void
    {
        $this->withoutVite();
        $this->seed(PortfolioWorkSeeder::class);

        $this->get('/portfolio/neon-pulse')
            ->assertOk()
            ->assertSee('Neon Pulse | Black Sky Portfolio')
            ->assertSee('CreativeWork');
    }
}

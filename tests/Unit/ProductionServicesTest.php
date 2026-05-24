<?php

namespace Tests\Unit;

use App\Support\ProductionServices;
use Tests\TestCase;

class ProductionServicesTest extends TestCase
{
    public function test_production_service_requirements_report_missing_external_services(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        config()->set('blacksky.production.enforce_services', true);
        config()->set('queue.default', 'database');
        config()->set('cache.default', 'database');
        config()->set('session.driver', 'database');
        config()->set('scout.driver', 'collection');
        config()->set('mail.default', 'log');
        config()->set('scout.meilisearch.key', null);
        config()->set('mail.from.address', 'hello@blacksky.test');

        $failures = app(ProductionServices::class)->failures();

        $this->assertNotEmpty($failures);
        $this->assertStringContainsString('Queue connection must be [redis]', implode("\n", $failures));
        $this->assertStringContainsString('Search driver must be [meilisearch]', implode("\n", $failures));
        $this->assertStringContainsString('Mail mailer must use a real transport', implode("\n", $failures));
    }

    public function test_production_service_requirements_pass_with_required_services(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        config()->set('blacksky.production.enforce_services', true);
        config()->set('queue.default', 'redis');
        config()->set('cache.default', 'redis');
        config()->set('session.driver', 'redis');
        config()->set('scout.driver', 'meilisearch');
        config()->set('mail.default', 'smtp');
        config()->set('scout.meilisearch.key', 'production-key');
        config()->set('mail.from.address', 'noreply@example.com');

        $this->assertSame([], app(ProductionServices::class)->failures());
    }
}

<?php

namespace App\Support;

use RuntimeException;

class ProductionServices
{
    /**
     * @return array<int, string>
     */
    public function failures(): array
    {
        if (! $this->shouldEnforce()) {
            return [];
        }

        $failures = [];
        $required = config('blacksky.production.required');

        $this->expectDriver($failures, 'queue.default', 'Queue connection', $required['queue_connection'] ?? 'redis');
        $this->expectDriver($failures, 'cache.default', 'Cache store', $required['cache_store'] ?? 'redis');
        $this->expectDriver($failures, 'session.driver', 'Session driver', $required['session_driver'] ?? 'redis');
        $this->expectDriver($failures, 'scout.driver', 'Search driver', $required['search_driver'] ?? 'meilisearch');

        if (in_array(config('mail.default'), config('blacksky.production.disallowed_mailers', []), true)) {
            $failures[] = 'Mail mailer must use a real transport; log/array mailers are local/testing only.';
        }

        if ((bool) config('blacksky.production.require_meilisearch_key') && blank(config('scout.meilisearch.key'))) {
            $failures[] = 'MEILISEARCH_KEY must be set for production search.';
        }

        if ((bool) config('blacksky.production.require_mail_from_address')) {
            $fromAddress = (string) config('mail.from.address');

            if (blank($fromAddress) || str_ends_with($fromAddress, '@blacksky.test')) {
                $failures[] = 'MAIL_FROM_ADDRESS must be a real production sender address.';
            }
        }

        return $failures;
    }

    public function ensureSatisfied(): void
    {
        $failures = $this->failures();

        if ($failures === []) {
            return;
        }

        throw new RuntimeException(
            "Production service requirements are not satisfied:\n- ".implode("\n- ", $failures),
        );
    }

    private function shouldEnforce(): bool
    {
        return app()->environment('production')
            && (bool) config('blacksky.production.enforce_services', true);
    }

    /**
     * @param  array<int, string>  $failures
     */
    private function expectDriver(array &$failures, string $configKey, string $label, string $expected): void
    {
        $actual = config($configKey);

        if ($actual !== $expected) {
            $failures[] = "{$label} must be [{$expected}] in production; current value is [{$actual}].";
        }
    }
}

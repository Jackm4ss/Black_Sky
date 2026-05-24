<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Laravel\Scout\Searchable;
use Throwable;

class PublicSearch
{
    public function apply(Builder $query, string $modelClass, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        if (! $this->shouldUseExternalSearch($modelClass)) {
            return $query->search($term);
        }

        try {
            $keys = $modelClass::search($term)
                ->take($this->resultLimit())
                ->keys()
                ->values();
        } catch (Throwable $exception) {
            if (app()->environment('production')) {
                throw $exception;
            }

            Log::warning('External search failed; falling back to database search.', [
                'model' => $modelClass,
                'driver' => config('scout.driver'),
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return $query->search($term);
        }

        if ($keys->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereKey($keys->all());
    }

    private function shouldUseExternalSearch(string $modelClass): bool
    {
        $uses = class_uses_recursive($modelClass);

        if (! in_array(Searchable::class, $uses, true)) {
            return false;
        }

        return in_array(
            config('scout.driver'),
            config('blacksky.search.external_drivers', []),
            true,
        );
    }

    private function resultLimit(): int
    {
        return max(1, (int) config('blacksky.search.public_result_limit', 1000));
    }
}

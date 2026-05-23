<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasGeneratedSlug
{
    protected static function bootHasGeneratedSlug(): void
    {
        static::saving(function (Model $model): void {
            if (filled($model->getAttribute('slug'))) {
                return;
            }

            $source = trim((string) $model->getAttribute(static::slugSourceColumn()));

            if ($source === '') {
                return;
            }

            $model->setAttribute('slug', static::makeGeneratedSlug($source, $model));
        });
    }

    protected static function slugSourceColumn(): string
    {
        return 'name';
    }

    protected static function slugFallback(): string
    {
        return 'item';
    }

    private static function makeGeneratedSlug(string $source, Model $model): string
    {
        $baseSlug = Str::slug($source) ?: static::slugFallback();
        $slug = $baseSlug;
        $suffix = 2;

        while (static::slugExists($slug, $model)) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    private static function slugExists(string $slug, Model $model): bool
    {
        $query = $model->newQuery()->where('slug', $slug);

        if ($model->exists) {
            $query->whereKeyNot($model->getKey());
        }

        return $query->exists();
    }
}

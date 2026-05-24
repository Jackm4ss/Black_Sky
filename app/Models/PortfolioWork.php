<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class PortfolioWork extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'year',
        'location',
        'role',
        'attendance',
        'excerpt',
        'description',
        'featured_image',
        'gallery_images',
        'accent_color',
        'status',
        'sort_order',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_image',
        'created_by',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'published_at' => 'datetime',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        if ($query->getConnection()->getDriverName() === 'mysql') {
            return $query->whereFullText(['title', 'excerpt', 'description', 'location', 'category'], $term);
        }

        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';

        return $query->where(function (Builder $query) use ($like): void {
            $query
                ->where('title', 'like', $like)
                ->orWhere('excerpt', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhere('location', 'like', $like)
                ->orWhere('category', 'like', $like);
        });
    }

    public function shouldBeSearchable(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    public function searchableAs(): string
    {
        return 'portfolio_works';
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'category' => $this->category,
            'year' => $this->year,
            'location' => $this->location,
            'role' => $this->role,
            'attendance' => $this->attendance,
            'excerpt' => $this->excerpt,
            'description' => strip_tags((string) $this->description),
            'status' => $this->status,
            'published_at' => $this->published_at?->timestamp,
            'created_at' => $this->created_at?->timestamp,
            'searchable_text' => trim(implode(' ', array_filter([
                $this->title,
                $this->category,
                $this->year,
                $this->location,
                $this->role,
                $this->excerpt,
                strip_tags((string) $this->description),
            ]))),
        ];
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        return self::publicAssetUrl($this->featured_image);
    }

    /**
     * @return array<int, string>
     */
    public function getGalleryImageUrlsAttribute(): array
    {
        return collect($this->gallery_images ?? [])
            ->map(fn (?string $path): ?string => self::publicAssetUrl($path))
            ->filter()
            ->values()
            ->all();
    }

    public function getSeoTitleAttribute(): string
    {
        return $this->meta_title ?: Str::limit($this->title.' | Black Sky Portfolio', 60, '');
    }

    public function getSeoDescriptionAttribute(): string
    {
        return $this->meta_description ?: Str::limit((string) ($this->excerpt ?: strip_tags((string) $this->description)), 160, '');
    }

    public static function publicAssetUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $path = (string) $path;

        if (Str::startsWith($path, ['http://', 'https://', 'data:', '/'])) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class BlogPost extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_image',
        'status',
        'author_id',
        'category_id',
        'view_count',
        'published_at',
        'scheduled_at',
        'created_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag', 'post_id', 'tag_id')
            ->withPivot('created_at');
    }

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
            return $query->whereFullText(['title', 'excerpt', 'content'], $term);
        }

        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';

        return $query->where(function (Builder $query) use ($like): void {
            $query
                ->where('title', 'like', $like)
                ->orWhere('excerpt', 'like', $like)
                ->orWhere('content', 'like', $like);
        });
    }

    public function shouldBeSearchable(): bool
    {
        $this->loadMissing(['author:id,is_active', 'category:id,is_active']);

        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->lte(now())
            && (bool) $this->author?->is_active
            && (bool) $this->category?->is_active;
    }

    public function searchableAs(): string
    {
        return 'blog_posts';
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $this->loadMissing([
            'author:id,name,slug,is_active',
            'category:id,name,slug,is_active',
            'tags:id,name,slug',
        ]);

        return [
            'id' => (int) $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => strip_tags((string) $this->content),
            'status' => $this->status,
            'author' => $this->author?->name,
            'author_slug' => $this->author?->slug,
            'category' => $this->category?->name,
            'category_slug' => $this->category?->slug,
            'tags' => $this->tags->pluck('name')->values()->all(),
            'tag_slugs' => $this->tags->pluck('slug')->values()->all(),
            'published_at' => $this->published_at?->timestamp,
            'created_at' => $this->created_at?->timestamp,
            'searchable_text' => trim(implode(' ', array_filter([
                $this->title,
                $this->excerpt,
                strip_tags((string) $this->content),
                $this->author?->name,
                $this->category?->name,
                $this->tags->pluck('name')->join(' '),
            ]))),
        ];
    }

    public function getReadingMinutesAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));

        return max(1, (int) ceil($wordCount / 220));
    }

    public function getSeoTitleAttribute(): string
    {
        return $this->meta_title ?: Str::limit($this->title.' | Black Sky Enterprise', 60, '');
    }

    public function getSeoDescriptionAttribute(): string
    {
        return $this->meta_description ?: Str::limit((string) $this->excerpt, 160, '');
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        return self::publicAssetUrl($this->featured_image);
    }

    public function getOgImageUrlAttribute(): ?string
    {
        return self::publicAssetUrl($this->og_image ?: $this->featured_image);
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

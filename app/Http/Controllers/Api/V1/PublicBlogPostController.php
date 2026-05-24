<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Support\PublicSearch;
use App\Support\RichContentSanitizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicBlogPostController extends Controller
{
    public function __construct(
        private readonly RichContentSanitizer $contentSanitizer,
        private readonly PublicSearch $publicSearch,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->integer('per_page', 9), 1), 18);
        $sort = (string) $request->string('sort', 'published');

        $postsQuery = $this->publicSearch->apply(
            $this->publishedPostsQuery()
                ->select([
                    'id',
                    'title',
                    'slug',
                    'excerpt',
                    'content',
                    'featured_image',
                    'og_image',
                    'meta_title',
                    'meta_description',
                    'status',
                    'author_id',
                    'category_id',
                    'view_count',
                    'published_at',
                    'updated_at',
                    'created_at',
                ])
                ->with([
                    'author:id,name,slug,bio,photo',
                    'category:id,name,slug,description',
                    'tags:id,name,slug',
                ]),
            BlogPost::class,
            (string) $request->string('search'),
        )
            ->when(
                $request->filled('category'),
                fn (Builder $query) => $query->whereHas(
                    'category',
                    fn (Builder $query) => $query->where('slug', (string) $request->string('category')),
                ),
            )
            ->when(
                $request->filled('tag'),
                fn (Builder $query) => $query->whereHas(
                    'tags',
                    fn (Builder $query) => $query->where('slug', (string) $request->string('tag')),
                ),
            );

        if ($sort === 'latest') {
            $postsQuery->orderByDesc('created_at')->orderByDesc('id');
        } else {
            $postsQuery->orderByDesc('published_at')->orderByDesc('id');
        }

        $posts = $postsQuery->cursorPaginate($perPage);

        return response()->json([
            'data' => $posts->getCollection()->map(fn (BlogPost $post): array => $this->postSummary($post))->values(),
            'meta' => [
                'per_page' => $perPage,
                'next_cursor' => $posts->nextCursor()?->encode(),
                'filters' => [
                    'categories' => BlogCategory::query()
                        ->select(['id', 'name', 'slug'])
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->get()
                        ->map(fn (BlogCategory $category): array => [
                            'name' => $category->name,
                            'slug' => $category->slug,
                        ])
                        ->values(),
                    'tags' => BlogTag::query()
                        ->select(['id', 'name', 'slug'])
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->get()
                        ->map(fn (BlogTag $tag): array => [
                            'name' => $tag->name,
                            'slug' => $tag->slug,
                        ])
                        ->values(),
                ],
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $post = $this->publishedPostsQuery()
            ->with([
                'author:id,name,slug,bio,photo,social_media',
                'category:id,name,slug,description',
                'tags:id,name,slug',
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'data' => [
                ...$this->postSummary($post),
                'content' => $this->contentSanitizer->clean($post->content),
                'meta_title' => $post->seo_title,
                'meta_description' => $post->seo_description,
                'meta_keywords' => $post->meta_keywords,
                'canonical_url' => $this->canonicalUrl($post),
                'og_image' => $post->og_image_url,
            ],
        ]);
    }

    private function publishedPostsQuery(): Builder
    {
        return BlogPost::query()
            ->published()
            ->whereHas('author', fn (Builder $query) => $query->where('is_active', true))
            ->whereHas('category', fn (Builder $query) => $query->where('is_active', true));
    }

    private function postSummary(BlogPost $post): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'featured_image' => $post->featured_image_url,
            'published_at' => $post->published_at?->toIso8601String(),
            'updated_at' => $post->updated_at?->toIso8601String(),
            'reading_minutes' => $post->reading_minutes,
            'view_count' => $post->view_count,
            'author' => $post->author ? [
                'name' => $post->author->name,
                'slug' => $post->author->slug,
                'bio' => $post->author->bio,
                'photo' => $post->author->photo,
            ] : null,
            'category' => $post->category ? [
                'name' => $post->category->name,
                'slug' => $post->category->slug,
            ] : null,
            'tags' => $post->tags->map(fn (BlogTag $tag): array => [
                'name' => $tag->name,
                'slug' => $tag->slug,
            ])->values(),
        ];
    }

    private function canonicalUrl(BlogPost $post): string
    {
        $canonicalUrl = $post->canonical_url;

        if (! $canonicalUrl || str_contains($canonicalUrl, '/blog/')) {
            return url('/news/'.$post->slug);
        }

        return $canonicalUrl;
    }
}

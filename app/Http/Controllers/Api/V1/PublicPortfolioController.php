<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PortfolioWork;
use App\Support\PublicSearch;
use App\Support\RichContentSanitizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicPortfolioController extends Controller
{
    public function __construct(
        private readonly RichContentSanitizer $contentSanitizer,
        private readonly PublicSearch $publicSearch,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->integer('per_page', 8), 1), 18);
        $baseQuery = $this->publishedPortfolioQuery();

        $works = $this->publicSearch->apply(
            clone $baseQuery,
            PortfolioWork::class,
            (string) $request->string('search'),
        )
            ->when(
                $request->filled('category'),
                fn (Builder $query) => $query->where('category', (string) $request->string('category')),
            )
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->cursorPaginate($perPage);

        return response()->json([
            'data' => $works->getCollection()
                ->map(fn (PortfolioWork $work): array => $this->workSummary($work))
                ->values(),
            'meta' => [
                'per_page' => $perPage,
                'next_cursor' => $works->nextCursor()?->encode(),
                'filters' => [
                    'categories' => (clone $baseQuery)
                        ->select('category')
                        ->distinct()
                        ->orderBy('category')
                        ->pluck('category')
                        ->values(),
                ],
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $work = $this->publishedPortfolioQuery()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'data' => [
                ...$this->workSummary($work),
                'description' => $this->contentSanitizer->clean($work->description),
                'gallery_images' => $work->gallery_image_urls,
                'meta_title' => $work->seo_title,
                'meta_description' => $work->seo_description,
                'meta_keywords' => $work->meta_keywords,
                'canonical_url' => $work->canonical_url ?: url('/portfolio/'.$work->slug),
                'og_image' => $work->og_image ? PortfolioWork::publicAssetUrl($work->og_image) : $work->featured_image_url,
            ],
        ]);
    }

    private function publishedPortfolioQuery(): Builder
    {
        return PortfolioWork::query()->published();
    }

    private function workSummary(PortfolioWork $work): array
    {
        return [
            'id' => $work->id,
            'title' => $work->title,
            'slug' => $work->slug,
            'category' => $work->category,
            'year' => $work->year,
            'location' => $work->location,
            'role' => $work->role,
            'attendance' => $work->attendance,
            'excerpt' => $work->excerpt,
            'featured_image' => $work->featured_image_url,
            'accent_color' => $work->accent_color,
            'published_at' => $work->published_at?->toIso8601String(),
            'updated_at' => $work->updated_at?->toIso8601String(),
        ];
    }
}

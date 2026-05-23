<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicEventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->integer('per_page', 12), 1), 24);
        $timeframe = $request->string('timeframe')->toString();
        $availability = $request->string('availability')->toString();
        $sort = $request->string('sort')->toString();
        $today = now()->toDateString();

        $baseQuery = $this->publishedEventsQuery();

        $eventsQuery = (clone $baseQuery)
            ->select([
                'id',
                'title',
                'slug',
                'subtitle',
                'venue',
                'city',
                'country_code',
                'genre',
                'start_date',
                'start_time',
                'end_date',
                'date_display',
                'status',
                'is_sold_out',
                'image_url',
                'accent_color',
                'glow_color',
                'vendor_url',
                'created_at',
            ])
            ->search((string) $request->string('search'))
            ->when($request->filled('city'), fn ($query) => $query->where('city', (string) $request->string('city')))
            ->when($request->filled('genre'), fn ($query) => $query->where('genre', (string) $request->string('genre')))
            ->when($timeframe === 'past', fn ($query) => $query->where('start_date', '<', $today))
            ->when($timeframe !== 'past', fn ($query) => $query->where('start_date', '>=', $today))
            ->when($availability === 'sold_out', fn ($query) => $query->where('is_sold_out', true))
            ->when($availability === 'available', fn ($query) => $query->where('is_sold_out', false))
            ->startingFrom((string) $request->string('date_from'))
            ->startingUntil((string) $request->string('date_to'));

        $orderedEventsQuery = match (true) {
            $sort === 'latest' => $eventsQuery
                ->orderByDesc('created_at')
                ->orderByDesc('id'),
            $timeframe === 'past' => $eventsQuery
                ->orderByDesc('start_date')
                ->orderByDesc('id'),
            default => $eventsQuery
                ->orderBy('start_date')
                ->orderBy('id'),
        };

        $events = $orderedEventsQuery->cursorPaginate($perPage);

        return response()->json([
            'data' => $events->getCollection()->map(fn (Event $event): array => $this->eventSummary($event))->values(),
            'meta' => [
                'per_page' => $perPage,
                'next_cursor' => $events->nextCursor()?->encode(),
                'filters' => [
                    'cities' => (clone $baseQuery)
                        ->select('city')
                        ->distinct()
                        ->orderBy('city')
                        ->pluck('city')
                        ->values(),
                    'genres' => (clone $baseQuery)
                        ->select('genre')
                        ->distinct()
                        ->orderBy('genre')
                        ->pluck('genre')
                        ->values(),
                ],
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $event = $this->publishedEventsQuery()
            ->with([
                'enabledSections:id,event_id,section_key,title,content,image_url,sort_order',
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'data' => [
                ...$this->eventSummary($event),
                'timezone' => $event->timezone,
                'organizer_name' => $event->organizer_name ?: 'Black Sky Enterprise',
                'organizer_url' => $event->organizer_url,
                'spotify_embed_url' => $event->spotify_embed_url,
                'sections' => $event->enabledSections
                    ->map(fn ($section): array => [
                        'id' => $section->id,
                        'section_key' => $section->section_key,
                        'title' => $section->title,
                        'content' => $section->content,
                        'image_url' => $section->image_url,
                    ])
                    ->values(),
                'meta_title' => $event->meta_title ?: $event->title . ' | Black Sky Enterprise',
                'meta_description' => $event->meta_description,
                'meta_keywords' => $event->meta_keywords,
                'canonical_url' => $event->canonical_url ?: url('/events/' . $event->slug),
                'og_image' => $event->og_image ?: $event->image_url,
            ],
        ]);
    }

    private function publishedEventsQuery(): Builder
    {
        return Event::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    private function eventSummary(Event $event): array
    {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'slug' => $event->slug,
            'subtitle' => $event->subtitle,
            'venue' => $event->venue,
            'city' => $event->city,
            'country_code' => $event->country_code,
            'genre' => $event->genre,
            'date' => $event->admin_date_label,
            'start_date' => $event->start_date->toDateString(),
            'start_time' => $event->start_time,
            'time' => $event->public_time_label,
            'end_date' => $event->end_date?->toDateString(),
            'status' => $event->is_sold_out ? 'sold_out' : $event->status,
            'image_url' => $event->image_url,
            'accent_color' => $event->accent_color,
            'glow_color' => $event->glow_color,
            'vendor_url' => $event->vendor_url,
        ];
    }
}

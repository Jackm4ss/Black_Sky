<?php

namespace App\Filament\Pages\Events;

use App\Models\Event;
use App\Support\ImageCompressionOptions;
use App\Support\SafeImageCompressor;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ListEvents extends Page
{
    use WithFileUploads;
    use WithPagination;

    private const EVENT_IMAGE_MAX_EDGE = 1920;

    private const EVENT_IMAGE_QUALITY = 82;

    private const EVENT_IMAGE_MAX_PIXELS = 50000000;

    private const EVENT_IMAGE_UPLOAD_MAX_KB = 102400;

    private const SEAT_MAP_IMAGE_MAX_EDGE = 2560;

    private const SEAT_MAP_IMAGE_QUALITY = 86;

    private const SEAT_MAP_IMAGE_MAX_PIXELS = 50000000;

    private const DEFAULT_EVENT_TIMEZONE = 'Asia/Kuala_Lumpur';

    private const DEFAULT_ORGANIZER_NAME = 'Black Sky Enterprise';

    private const DEFAULT_IMPORTANT_INFORMATION = "- Ticket purchases, payment confirmation, refunds, and ticket validity are handled by the official ticketing vendor.\n- Entry requirements, venue rules, seating changes, and event updates follow the vendor or venue policy.\n- Please review the vendor checkout page and event terms before completing your purchase.";

    private const EVENT_GENRES = [
        'FESTIVAL' => 'Festival',
        'ARENA SHOW' => 'Arena Show',
        'CONCERT' => 'Concert',
        'LIVE SHOW' => 'Live Show',
        'TOUR' => 'Tour',
        'RAVE' => 'Rave',
        'CLUB NIGHT' => 'Club Night',
        'OUTDOOR' => 'Outdoor',
        'K-POP' => 'K-Pop',
        'J-POP' => 'J-Pop',
        'ASIAN POP' => 'Asian Pop',
        'POP' => 'Pop',
        'ROCK' => 'Rock',
        'INDIE' => 'Indie',
        'HIP-HOP' => 'Hip-Hop',
        'R&B' => 'R&B',
        'EDM' => 'EDM',
        'HOUSE' => 'House',
        'TECHNO' => 'Techno',
        'TRANCE' => 'Trance',
        'JAZZ' => 'Jazz',
        'ACOUSTIC' => 'Acoustic',
        'ORCHESTRA' => 'Orchestra',
        'THEATRE' => 'Theatre',
        'COMEDY' => 'Comedy',
        'FAN MEETING' => 'Fan Meeting',
        'CONFERENCE' => 'Conference',
        'EXHIBITION' => 'Exhibition',
    ];

    private const EVENT_SECTION_TYPES = [
        'about' => 'About',
        'event_details' => 'Event Details',
        'on_sale_details' => 'On-Sale Details',
        'seat_map_ticket_pricing' => 'Seat Map & Ticket Pricing',
        'ticket_pricing' => 'Ticket Pricing',
        'location' => 'Location',
        'ticketing_information' => 'Ticketing Information',
        'important_information' => 'Important Information',
        'admission_policy' => 'Admission Policy',
        'fan_benefit_information' => 'Fan Benefit Information',
        'event_guide' => 'Event Guide',
        'custom' => 'Custom Section',
    ];

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Events';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'events';

    protected static string $view = 'filament.pages.events.list-events';

    protected ?string $maxContentWidth = 'full';

    #[Url(except: '')]
    public string $search = '';

    #[Url(as: 'from', except: '')]
    public string $dateFrom = '';

    #[Url(as: 'to', except: '')]
    public string $dateTo = '';

    #[Url(as: 'per_page', except: 10)]
    public int $perPage = 10;

    public bool $isFormOpen = false;

    public bool $isDeleteOpen = false;

    public ?int $editingEventId = null;

    public ?int $deletingEventId = null;

    public string $deletingEventTitle = '';

    public mixed $eventImage = null;

    public mixed $seatMapImage = null;

    /**
     * @var array<string, mixed>
     */
    public array $form = [];

    public function mount(): void
    {
        $this->resetEventForm();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Events';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public static function getNavigationItemActiveRoutePattern(): string
    {
        return 'filament.admin.pages.events*';
    }

    /**
     * @return array<string, string>
     */
    public function genreOptions(): array
    {
        return self::EVENT_GENRES;
    }

    public function updatedSearch(): void
    {
        $this->resetPage('eventsPage');
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage('eventsPage');
    }

    public function updatedDateTo(): void
    {
        $this->resetPage('eventsPage');
    }

    public function updatedPerPage(): void
    {
        $this->perPage = in_array($this->perPage, [5, 10, 25, 50], true) ? $this->perPage : 10;

        $this->resetPage('eventsPage');
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->perPage = 10;

        $this->resetPage('eventsPage');
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->editingEventId = null;
        $this->eventImage = null;
        $this->seatMapImage = null;
        $this->resetEventForm();
        $this->isFormOpen = true;
    }

    public function openEdit(int $eventId): void
    {
        $event = Event::query()
            ->with('sections')
            ->findOrFail($eventId);

        $this->resetValidation();
        $this->eventImage = null;
        $this->seatMapImage = null;
        $this->editingEventId = $event->id;
        $sections = $this->eventSectionsByKey($event);
        $seatMapSection = $sections['seat_map_ticket_pricing'] ?? $sections['ticket_pricing'] ?? [];

        $this->form = [
            'title' => $event->title,
            'slug' => $event->slug,
            'subtitle' => $event->subtitle,
            'venue' => $event->venue,
            'city' => $event->city,
            'country_code' => $event->country_code,
            'genre' => $event->genre,
            'start_date' => $event->start_date?->toDateString(),
            'start_time' => $this->timeForInput($event->start_time),
            'end_date' => $event->end_date?->toDateString(),
            'date_display' => $event->date_display,
            'timezone' => $event->timezone,
            'status' => $event->status,
            'is_sold_out' => $event->is_sold_out,
            'image_url' => $event->image_url,
            'accent_color' => $event->accent_color,
            'vendor_url' => $event->vendor_url,
            'organizer_name' => $event->organizer_name,
            'organizer_url' => $event->organizer_url,
            'spotify_embed_url' => $event->spotify_embed_url,
            'detail_event_details' => $sections['event_details']['content'] ?? '',
            'google_maps_url' => $sections['location']['content'] ?? '',
            'ticket_pricing' => $sections['ticket_pricing']['content'] ?? '',
            'seat_map_image_url' => $seatMapSection['image_url'] ?? '',
            'meta_title' => $event->meta_title,
            'meta_description' => $event->meta_description,
            'meta_keywords' => $event->meta_keywords,
            'canonical_url' => $event->canonical_url,
            'og_image' => $event->og_image,
        ];
        $this->isFormOpen = true;
    }

    public function closeEventForm(): void
    {
        $this->isFormOpen = false;
        $this->editingEventId = null;
        $this->eventImage = null;
        $this->seatMapImage = null;
        $this->resetValidation();
        $this->resetEventForm();
    }

    public function saveEvent(): mixed
    {
        $this->form['slug'] = filled($this->form['slug'] ?? null)
            ? Str::slug((string) $this->form['slug'])
            : Str::slug((string) ($this->form['title'] ?? ''));

        $validated = $this->validate()['form'];

        $slug = $validated['slug'];

        $event = $this->editingEventId
            ? Event::query()->findOrFail($this->editingEventId)
            : new Event;

        $oldImageUrl = $event->image_url;
        $imageUrl = filled($validated['image_url'] ?? null) ? $validated['image_url'] : $event->image_url;
        $oldSeatMapImageUrl = $this->editingEventId
            ? $event->sections()
                ->whereIn('section_key', ['seat_map_ticket_pricing', 'ticket_pricing'])
                ->whereNotNull('image_url')
                ->value('image_url')
            : null;
        $seatMapImageUrl = filled($validated['seat_map_image_url'] ?? null)
            ? $validated['seat_map_image_url']
            : null;

        if ($this->eventImage instanceof TemporaryUploadedFile) {
            $imageUrl = $this->storeCompressedEventImage($this->eventImage, $slug);
        }

        if ($this->seatMapImage instanceof TemporaryUploadedFile) {
            $seatMapImageUrl = $this->storeCompressedSeatMapImage($this->seatMapImage, $slug);
        }

        $accentColor = $validated['accent_color'] ?? '#0EA5E9';
        $spotifyEmbedUrl = $this->normalizeSpotifyEmbedUrl($validated['spotify_embed_url'] ?? null);
        $validated['google_maps_url'] = $this->normalizeGoogleMapsUrl($validated['google_maps_url'] ?? null);
        $sections = $this->eventSectionsFromForm($validated, $seatMapImageUrl);
        unset(
            $validated['detail_event_details'],
            $validated['google_maps_url'],
            $validated['ticket_pricing'],
            $validated['seat_map_image_url']
        );

        $event->fill([
            ...$validated,
            'slug' => $slug,
            'country_code' => Str::upper($validated['country_code']),
            'genre' => Str::upper($validated['genre']),
            'start_time' => filled($validated['start_time'] ?? null) ? $validated['start_time'] : null,
            'end_date' => filled($validated['end_date'] ?? null) ? $validated['end_date'] : null,
            'date_display' => null,
            'timezone' => filled($validated['timezone'] ?? null) ? $validated['timezone'] : self::DEFAULT_EVENT_TIMEZONE,
            'is_sold_out' => (bool) ($validated['is_sold_out'] ?? false),
            'image_url' => $imageUrl,
            'accent_color' => $accentColor,
            'organizer_name' => self::DEFAULT_ORGANIZER_NAME,
            'organizer_url' => null,
            'spotify_embed_url' => $spotifyEmbedUrl,
            'glow_color' => $this->glowColorFromAccent($accentColor),
            'published_at' => $validated['status'] === 'published'
                ? ($event->published_at ?? now())
                : null,
            'meta_title' => $this->defaultMetaTitle($validated),
            'meta_description' => $this->defaultMetaDescription($validated),
            'meta_keywords' => $this->defaultMetaKeywords($validated),
            'canonical_url' => url('/events/'.$slug),
            'og_image' => $imageUrl,
        ]);
        $event->save();
        $this->syncEventSections($event, $sections);

        if ($this->eventImage instanceof TemporaryUploadedFile && $oldImageUrl !== $imageUrl) {
            $this->deleteStoredEventImage($oldImageUrl);
        }

        if ($this->seatMapImage instanceof TemporaryUploadedFile && $oldSeatMapImageUrl !== $seatMapImageUrl) {
            $this->deleteStoredEventImage($oldSeatMapImageUrl);
        }

        Notification::make()
            ->title('Event saved')
            ->body($event->title.' has been saved.')
            ->success()
            ->send();

        return $this->afterEventSaved($event);
    }

    protected function afterEventSaved(Event $event): mixed
    {
        $this->isFormOpen = false;
        $this->editingEventId = null;
        $this->eventImage = null;
        $this->seatMapImage = null;
        $this->resetEventForm();
        $this->resetPage('eventsPage');

        return null;
    }

    private function glowColorFromAccent(string $accentColor): string
    {
        if (! preg_match('/^#([0-9A-Fa-f]{6})$/', $accentColor, $matches)) {
            return 'rgba(14,165,233,0.45)';
        }

        $hex = $matches[1];

        return sprintf(
            'rgba(%d,%d,%d,0.45)',
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        );
    }

    private function timeForInput(mixed $time): string
    {
        if (blank($time)) {
            return '';
        }

        $value = (string) $time;

        return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d/', $value) === 1
            ? substr($value, 0, 5)
            : '';
    }

    public function confirmDelete(int $eventId): void
    {
        $event = Event::query()->findOrFail($eventId);

        $this->deletingEventId = $eventId;
        $this->deletingEventTitle = $event->title;
        $this->isDeleteOpen = true;
    }

    public function closeDeleteModal(): void
    {
        $this->deletingEventId = null;
        $this->deletingEventTitle = '';
        $this->isDeleteOpen = false;
    }

    public function deleteEvent(): void
    {
        $event = Event::query()
            ->with('sections')
            ->findOrFail($this->deletingEventId);
        $title = $event->title;
        $imageUrls = collect([$event->image_url])
            ->merge($event->sections->pluck('image_url'))
            ->filter()
            ->unique()
            ->values();

        $event->delete();

        $imageUrls->each(fn (mixed $imageUrl) => $this->deleteStoredEventImage(is_string($imageUrl) ? $imageUrl : null));

        $this->closeDeleteModal();
        $this->resetPage('eventsPage');

        Notification::make()
            ->title('Event deleted')
            ->body($title.' has been removed.')
            ->success()
            ->send();
    }

    public function events(): LengthAwarePaginator
    {
        $perPage = in_array($this->perPage, [5, 10, 25, 50], true) ? $this->perPage : 10;

        return Event::query()
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
                'end_date',
                'date_display',
                'status',
                'is_sold_out',
                'image_url',
                'accent_color',
                'published_at',
            ])
            ->search($this->search)
            ->startingFrom($this->dateFrom)
            ->startingUntil($this->dateTo)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'eventsPage');
    }

    protected function rules(): array
    {
        return [
            'form.title' => ['required', 'string', 'max:255'],
            'form.slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('events', 'slug')->ignore($this->editingEventId),
            ],
            'form.subtitle' => ['nullable', 'string', 'max:255'],
            'form.venue' => ['required', 'string', 'max:255'],
            'form.city' => ['required', 'string', 'max:255'],
            'form.country_code' => ['required', 'string', 'size:2', 'regex:/^[A-Za-z]{2}$/'],
            'form.genre' => ['required', 'string', 'max:80', Rule::in(array_keys(self::EVENT_GENRES))],
            'form.start_date' => ['required', 'date'],
            'form.start_time' => ['nullable', 'date_format:H:i'],
            'form.end_date' => ['nullable', 'date', 'after_or_equal:form.start_date'],
            'form.date_display' => ['nullable', 'string', 'max:255'],
            'form.timezone' => ['required', 'string', 'max:64'],
            'form.status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'form.is_sold_out' => ['boolean'],
            'form.image_url' => ['nullable', 'string', 'max:2048'],
            'eventImage' => [
                Rule::requiredIf(! $this->editingEventId && blank($this->form['image_url'] ?? null)),
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:'.self::EVENT_IMAGE_UPLOAD_MAX_KB,
            ],
            'form.accent_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'form.vendor_url' => ['nullable', 'url', 'max:2048'],
            'form.organizer_name' => ['nullable', 'string', 'max:255'],
            'form.organizer_url' => ['nullable', 'url', 'max:2048'],
            'form.spotify_embed_url' => ['nullable', 'url', 'max:2048'],
            'form.detail_event_details' => ['nullable', 'string', 'max:20000'],
            'form.google_maps_url' => ['nullable', 'url', 'max:2048'],
            'form.ticket_pricing' => ['nullable', 'string', 'max:20000'],
            'form.seat_map_image_url' => ['nullable', 'string', 'max:2048'],
            'seatMapImage' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:'.self::EVENT_IMAGE_UPLOAD_MAX_KB,
            ],
            'form.meta_title' => ['nullable', 'string', 'max:255'],
            'form.meta_description' => ['nullable', 'string', 'max:1000'],
            'form.meta_keywords' => ['nullable', 'string', 'max:255'],
            'form.canonical_url' => ['nullable', 'url', 'max:255'],
            'form.og_image' => ['nullable', 'string', 'max:2048'],
        ];
    }

    private function resetEventForm(): void
    {
        $this->form = [
            'title' => '',
            'slug' => '',
            'subtitle' => '',
            'venue' => '',
            'city' => '',
            'country_code' => '',
            'genre' => '',
            'start_date' => '',
            'start_time' => '',
            'end_date' => '',
            'date_display' => '',
            'timezone' => self::DEFAULT_EVENT_TIMEZONE,
            'status' => 'published',
            'is_sold_out' => false,
            'image_url' => '',
            'accent_color' => '#0EA5E9',
            'vendor_url' => '',
            'organizer_name' => self::DEFAULT_ORGANIZER_NAME,
            'organizer_url' => '',
            'spotify_embed_url' => '',
            'detail_event_details' => '',
            'google_maps_url' => '',
            'ticket_pricing' => "- General Admission: RM 188\n- Premium Zone: RM 328\n- VIP Deck: RM 488",
            'seat_map_image_url' => '',
            'meta_title' => '',
            'meta_description' => '',
            'meta_keywords' => '',
            'canonical_url' => '',
            'og_image' => '',
        ];
    }

    /**
     * Store one optimized artwork for public use. Admins can upload original
     * 4K/8K artwork; the saved asset is capped for web delivery.
     */
    private function storeCompressedEventImage(TemporaryUploadedFile $upload, string $slug): string
    {
        return $this->storeCompressedImage(
            upload: $upload,
            slug: $slug,
            directory: 'events',
            errorField: 'eventImage',
            maxEdge: self::EVENT_IMAGE_MAX_EDGE,
            quality: self::EVENT_IMAGE_QUALITY,
            label: 'artwork',
        );
    }

    private function storeCompressedSeatMapImage(TemporaryUploadedFile $upload, string $slug): string
    {
        return $this->storeCompressedImage(
            upload: $upload,
            slug: $slug,
            directory: 'events/seat-maps',
            errorField: 'seatMapImage',
            maxEdge: self::SEAT_MAP_IMAGE_MAX_EDGE,
            quality: self::SEAT_MAP_IMAGE_QUALITY,
            label: 'seat map',
        );
    }

    private function storeCompressedImage(
        TemporaryUploadedFile $upload,
        string $slug,
        string $directory,
        string $errorField,
        int $maxEdge,
        int $quality,
        string $label,
    ): string {
        return $this->imageCompressor()->storeUploaded(
            $upload,
            new ImageCompressionOptions(
                directory: $directory,
                filenamePrefix: $slug,
                errorField: $errorField,
                label: $label,
                maxEdge: $maxEdge,
                quality: $quality,
                maxPixels: str_contains($directory, 'seat-maps')
                    ? self::SEAT_MAP_IMAGE_MAX_PIXELS
                    : self::EVENT_IMAGE_MAX_PIXELS,
                returnUrl: true,
                timeLimitSeconds: 120,
            ),
        );
    }

    private function deleteStoredEventImage(?string $imageUrl): void
    {
        $this->imageCompressor()->deletePublicPath($imageUrl, 'events/');
    }

    private function imageCompressor(): SafeImageCompressor
    {
        return app(SafeImageCompressor::class);
    }

    private function normalizeSpotifyEmbedUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $parts = parse_url(trim($url));
        $host = Str::lower((string) ($parts['host'] ?? ''));
        $path = trim((string) ($parts['path'] ?? ''), '/');

        if (! in_array($host, ['open.spotify.com', 'www.open.spotify.com'], true) || $path === '') {
            throw ValidationException::withMessages([
                'form.spotify_embed_url' => 'Use a Spotify URL from open.spotify.com.',
            ]);
        }

        $segments = explode('/', $path);

        if (($segments[0] ?? null) === 'embed') {
            $type = $segments[1] ?? '';
            $id = $segments[2] ?? '';
        } else {
            $type = $segments[0] ?? '';
            $id = $segments[1] ?? '';
        }

        if (! in_array($type, ['artist', 'album', 'playlist', 'track', 'show', 'episode'], true) || $id === '') {
            throw ValidationException::withMessages([
                'form.spotify_embed_url' => 'Use a Spotify artist, album, playlist, track, show, or episode URL.',
            ]);
        }

        return 'https://open.spotify.com/embed/'.$type.'/'.$id.'?utm_source=generator';
    }

    private function normalizeGoogleMapsUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $trimmedUrl = trim($url);
        $parts = parse_url($trimmedUrl);
        $scheme = Str::lower((string) ($parts['scheme'] ?? ''));
        $host = Str::lower((string) ($parts['host'] ?? ''));
        $path = (string) ($parts['path'] ?? '');
        $isGoogleMapsUrl = in_array($host, ['maps.app.goo.gl', 'maps.google.com'], true)
            || ($host === 'goo.gl' && str_starts_with($path, '/maps'))
            || (str_contains($host, 'google.') && str_contains($path, '/maps'));

        if (! in_array($scheme, ['http', 'https'], true) || ! $isGoogleMapsUrl) {
            throw ValidationException::withMessages([
                'form.google_maps_url' => 'Use a valid Google Maps link.',
            ]);
        }

        return $trimmedUrl;
    }

    /**
     * @param  array<string, mixed>  $event
     */
    private function defaultMetaTitle(array $event): string
    {
        return Str::limit($event['title'].' | Black Sky Enterprise', 255, '');
    }

    /**
     * @param  array<string, mixed>  $event
     */
    private function defaultMetaDescription(array $event): string
    {
        $parts = array_filter([
            $event['subtitle'] ?? null,
            filled($event['venue'] ?? null) ? 'Live at '.$event['venue'] : null,
            filled($event['city'] ?? null) ? $event['city'] : null,
        ]);

        return Str::limit(implode(' - ', $parts) ?: $event['title'], 1000, '');
    }

    /**
     * @param  array<string, mixed>  $event
     */
    private function defaultMetaKeywords(array $event): string
    {
        $keywords = array_unique(array_filter([
            'Black Sky',
            self::DEFAULT_ORGANIZER_NAME,
            'event',
            'concert',
            $event['title'] ?? null,
            $event['genre'] ?? null,
            $event['city'] ?? null,
            $event['venue'] ?? null,
        ]));

        return Str::limit(implode(', ', $keywords), 255, '');
    }

    /**
     * @param  array<int, array<string, mixed>>  $sections
     */
    private function syncEventSections(Event $event, array $sections): void
    {
        $event->sections()->delete();

        foreach (array_values($sections) as $index => $section) {
            $content = trim((string) ($section['content'] ?? ''));
            $imageUrl = trim((string) ($section['image_url'] ?? ''));

            if ($content === '' && $imageUrl === '') {
                continue;
            }

            $sectionKey = (string) ($section['section_key'] ?? 'custom');
            $title = trim((string) ($section['title'] ?? ''));

            if (! array_key_exists($sectionKey, self::EVENT_SECTION_TYPES)) {
                $sectionKey = 'custom';
            }

            $event->sections()->create([
                'section_key' => $sectionKey,
                'title' => $title !== '' ? $title : self::EVENT_SECTION_TYPES[$sectionKey],
                'content' => $content,
                'image_url' => $imageUrl !== '' ? $imageUrl : null,
                'sort_order' => $index,
                'is_enabled' => (bool) ($section['is_enabled'] ?? true),
            ]);
        }
    }

    /**
     * @return array<string, array{content: string, image_url: string}>
     */
    private function eventSectionsByKey(Event $event): array
    {
        return $event->sections
            ->mapWithKeys(fn ($section): array => [
                $section->section_key => [
                    'content' => (string) ($section->content ?? ''),
                    'image_url' => (string) ($section->image_url ?? ''),
                ],
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $form
     * @return array<int, array<string, mixed>>
     */
    private function eventSectionsFromForm(array $form, ?string $seatMapImageUrl): array
    {
        return [
            [
                'section_key' => 'about',
                'title' => self::EVENT_SECTION_TYPES['about'],
                'content' => trim((string) ($form['subtitle'] ?? '')),
                'image_url' => null,
                'is_enabled' => true,
            ],
            [
                'section_key' => 'event_details',
                'title' => self::EVENT_SECTION_TYPES['event_details'],
                'content' => trim((string) ($form['detail_event_details'] ?? '')),
                'image_url' => null,
                'is_enabled' => true,
            ],
            [
                'section_key' => 'location',
                'title' => self::EVENT_SECTION_TYPES['location'],
                'content' => trim((string) ($form['google_maps_url'] ?? '')),
                'image_url' => null,
                'is_enabled' => true,
            ],
            [
                'section_key' => 'seat_map_ticket_pricing',
                'title' => self::EVENT_SECTION_TYPES['seat_map_ticket_pricing'],
                'content' => '',
                'image_url' => $seatMapImageUrl,
                'is_enabled' => true,
            ],
            [
                'section_key' => 'ticket_pricing',
                'title' => self::EVENT_SECTION_TYPES['ticket_pricing'],
                'content' => trim((string) ($form['ticket_pricing'] ?? '')),
                'image_url' => null,
                'is_enabled' => true,
            ],
            [
                'section_key' => 'important_information',
                'title' => self::EVENT_SECTION_TYPES['important_information'],
                'content' => self::DEFAULT_IMPORTANT_INFORMATION,
                'image_url' => null,
                'is_enabled' => true,
            ],
        ];
    }
}

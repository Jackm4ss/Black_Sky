<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Fortify\UpdateUserPassword;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Bookmark;
use App\Models\Event;
use App\Models\SyncedTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class UserDashboardController extends Controller
{
    private const AVATAR_IMAGE_MAX_EDGE = 640;
    private const AVATAR_IMAGE_QUALITY = 84;
    private const AVATAR_IMAGE_MAX_PIXELS = 50000000;
    private const AVATAR_UPLOAD_MAX_KILOBYTES = 204800;
    private const AVATAR_ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    public function show(Request $request): JsonResponse
    {
        $user = $this->memberUser($request);
        $today = now()->toDateString();

        $bookmarks = Bookmark::query()
            ->where('user_id', $user->id)
            ->with(['event' => fn ($query) => $query->select($this->eventColumns())])
            ->latest()
            ->limit(6)
            ->get();

        $transactions = SyncedTransaction::query()
            ->where('user_id', $user->id)
            ->with(['event' => fn ($query) => $query->select($this->eventColumns())])
            ->latest('purchased_at')
            ->latest()
            ->limit(6)
            ->get();

        $upcomingEvents = $this->publishedEventsQuery()
            ->where('start_date', '>=', $today)
            ->orderBy('start_date')
            ->orderBy('id')
            ->limit(4)
            ->get();

        $notifications = $user->notifications()
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn ($notification): array => [
                'id' => $notification->id,
                'title' => (string) data_get($notification->data, 'title', 'Black Sky update'),
                'body' => (string) data_get($notification->data, 'body', 'New member notification.'),
                'type' => (string) data_get($notification->data, 'type', 'notice'),
                'read_at' => $notification->read_at?->toISOString(),
                'created_at' => $notification->created_at?->toISOString(),
            ])
            ->values();

        return response()->json([
            'data' => [
                'user' => UserResource::make($user),
                'stats' => [
                    'tickets' => SyncedTransaction::query()->where('user_id', $user->id)->count(),
                    'saved_events' => Bookmark::query()->where('user_id', $user->id)->count(),
                    'unread_notifications' => $user->unreadNotifications()->count(),
                    'upcoming_events' => $upcomingEvents->count(),
                ],
                'tickets' => $transactions
                    ->map(fn (SyncedTransaction $transaction): array => $this->ticketPayload($transaction))
                    ->values(),
                'saved_events' => $bookmarks
                    ->map(fn (Bookmark $bookmark): array => [
                        'id' => $bookmark->id,
                        'created_at' => $bookmark->created_at?->toISOString(),
                        'event' => $bookmark->event ? $this->eventPayload($bookmark->event) : null,
                    ])
                    ->filter(fn (array $bookmark): bool => $bookmark['event'] !== null)
                    ->values(),
                'notifications' => $notifications,
                'upcoming_events' => $upcomingEvents
                    ->map(fn (Event $event): array => $this->eventPayload($event))
                    ->values(),
            ],
        ]);
    }

    public function updateAccount(Request $request): JsonResponse
    {
        $user = $this->memberUser($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'gender' => ['nullable', 'string', Rule::in([
                'male',
                'female',
                'non_binary',
                'prefer_not_to_say',
            ])],
            'avatar' => [
                'nullable',
                'file',
                'image',
                'mimetypes:' . implode(',', self::AVATAR_ALLOWED_MIME_TYPES),
                'max:' . self::AVATAR_UPLOAD_MAX_KILOBYTES,
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $emailChanged = $user->email !== $validated['email'];
        $avatarUrl = null;
        $previousAvatar = $user->avatar;

        if ($request->hasFile('avatar')) {
            $avatarUrl = $this->storeCompressedAvatar($request->file('avatar'), $user);
        }

        $user->forceFill([
            'name' => $validated['name'],
            'email' => Str::lower($validated['email']),
            'phone' => ($validated['phone'] ?? null) ?: null,
            'registration_country_code' => filled($validated['country_code'] ?? null)
                ? Str::upper((string) $validated['country_code'])
                : null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'avatar' => $avatarUrl ?? $user->avatar,
            'email_verified_at' => $emailChanged ? null : $user->email_verified_at,
        ])->save();

        if ($avatarUrl) {
            $this->deleteStoredAvatar($previousAvatar);
        }

        return response()->json([
            'data' => UserResource::make($user->fresh()->loadMissing('roles')),
            'message' => 'Account details updated.',
        ]);
    }

    public function updatePassword(Request $request, UpdateUserPassword $updater): JsonResponse
    {
        $user = $this->memberUser($request);

        $updater->update($user, [
            'current_password' => (string) $request->input('current_password'),
            'password' => (string) $request->input('password'),
            'password_confirmation' => (string) $request->input('password_confirmation'),
        ]);

        return response()->json([
            'message' => 'Password updated.',
        ]);
    }

    public function destroyAccount(Request $request): JsonResponse
    {
        $user = $this->memberUser($request);

        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Hash::check((string) $validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'The password does not match your account.',
            ]);
        }

        $avatar = $user->avatar;

        $user->tokens()->delete();
        $user->delete();
        $this->deleteStoredAvatar($avatar);

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'message' => 'Account removed.',
        ]);
    }

    public function storeBookmark(Request $request, Event $event): JsonResponse
    {
        $user = $this->memberUser($request);

        abort_unless($this->publishedEventsQuery()->whereKey($event->id)->exists(), 404);

        $bookmark = Bookmark::query()->firstOrCreate([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        return response()->json([
            'data' => [
                'id' => $bookmark->id,
                'event' => $this->eventPayload($event),
            ],
        ], 201);
    }

    public function destroyBookmark(Request $request, Event $event): JsonResponse
    {
        $user = $this->memberUser($request);

        Bookmark::query()
            ->where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->delete();

        return response()->json(['message' => 'Saved event removed.']);
    }

    private function memberUser(Request $request): User
    {
        /** @var User $user */
        $user = $request->user()->loadMissing('roles');

        abort_unless($user->hasRole('user'), 403, 'Only member accounts can access this dashboard.');

        return $user;
    }

    private function storeCompressedAvatar(UploadedFile $upload, User $user): string
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(90);

        if (! $upload->isValid()) {
            throw ValidationException::withMessages([
                'avatar' => 'The profile image upload did not complete.',
            ]);
        }

        $sourcePath = $upload->getRealPath();
        $reportedMimeType = (string) $upload->getMimeType();

        if (! is_string($sourcePath) || ! is_file($sourcePath)) {
            throw ValidationException::withMessages([
                'avatar' => 'The profile image could not be read.',
            ]);
        }

        if (! in_array($reportedMimeType, self::AVATAR_ALLOWED_MIME_TYPES, true)) {
            throw ValidationException::withMessages([
                'avatar' => 'Upload a JPG, PNG, WEBP, or GIF profile image.',
            ]);
        }

        $imageInfo = @getimagesize($sourcePath);

        if ($imageInfo === false) {
            throw ValidationException::withMessages([
                'avatar' => 'The profile image could not be read.',
            ]);
        }

        [$sourceWidth, $sourceHeight, $imageType] = $imageInfo;
        $detectedMimeType = (string) ($imageInfo['mime'] ?? '');
        $sourcePixels = (int) $sourceWidth * (int) $sourceHeight;

        if (
            $sourceWidth < 1 ||
            $sourceHeight < 1 ||
            $sourcePixels > self::AVATAR_IMAGE_MAX_PIXELS ||
            ! in_array($detectedMimeType, self::AVATAR_ALLOWED_MIME_TYPES, true)
        ) {
            throw ValidationException::withMessages([
                'avatar' => 'Upload a JPG, PNG, WEBP, or GIF profile image under the supported resolution limit.',
            ]);
        }

        $source = match ($imageType) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($sourcePath),
            IMAGETYPE_GIF => @imagecreatefromgif($sourcePath),
            default => false,
        };

        if (! $source) {
            throw ValidationException::withMessages([
                'avatar' => 'Upload a JPG, PNG, WEBP, or GIF profile image.',
            ]);
        }

        $scale = min(1, self::AVATAR_IMAGE_MAX_EDGE / max($sourceWidth, $sourceHeight));
        $targetWidth = max(1, (int) round($sourceWidth * $scale));
        $targetHeight = max(1, (int) round($sourceHeight * $scale));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, imagecolorallocatealpha($target, 0, 0, 0, 127));
        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

        $relativeDirectory = 'profile-avatars/' . $user->id;
        $relativePath = $relativeDirectory . '/' . Str::uuid() . '.webp';

        Storage::disk('public')->makeDirectory($relativeDirectory);

        $stored = @imagewebp($target, Storage::disk('public')->path($relativePath), self::AVATAR_IMAGE_QUALITY);

        imagedestroy($source);
        imagedestroy($target);

        if (! $stored) {
            throw ValidationException::withMessages([
                'avatar' => 'The profile image could not be compressed.',
            ]);
        }

        return Storage::disk('public')->url($relativePath);
    }

    private function deleteStoredAvatar(?string $avatarUrl): void
    {
        if (blank($avatarUrl)) {
            return;
        }

        $path = parse_url($avatarUrl, PHP_URL_PATH);

        if (! is_string($path) || ! str_contains($path, '/storage/profile-avatars/')) {
            return;
        }

        $relativePath = str_replace('\\', '/', Str::after($path, '/storage/'));

        if (
            ! Str::startsWith($relativePath, 'profile-avatars/') ||
            str_contains($relativePath, '..') ||
            str_contains($relativePath, "\0")
        ) {
            return;
        }

        Storage::disk('public')->delete($relativePath);
    }

    /**
     * @return list<string>
     */
    private function eventColumns(): array
    {
        return [
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
            'published_at',
        ];
    }

    private function publishedEventsQuery(): Builder
    {
        return Event::query()
            ->select($this->eventColumns())
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    private function eventPayload(Event $event): array
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
            'start_date' => $event->start_date?->toDateString(),
            'time' => $event->public_time_label,
            'status' => $event->is_sold_out ? 'sold_out' : $event->status,
            'image_url' => $event->image_url,
            'accent_color' => $event->accent_color,
            'glow_color' => $event->glow_color,
            'vendor_url' => $event->vendor_url,
        ];
    }

    private function ticketPayload(SyncedTransaction $transaction): array
    {
        return [
            'id' => $transaction->id,
            'vendor' => $transaction->vendor,
            'external_order_id' => $transaction->external_order_id,
            'event_title' => $transaction->event?->title ?: $transaction->event_title,
            'ticket_type' => $transaction->ticket_type,
            'quantity' => $transaction->quantity,
            'total_amount' => $transaction->total_amount,
            'currency' => $transaction->currency,
            'status' => $transaction->status,
            'purchased_at' => $transaction->purchased_at?->toISOString(),
            'event' => $transaction->event ? $this->eventPayload($transaction->event) : null,
        ];
    }
}

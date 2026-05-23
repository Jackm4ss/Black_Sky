<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Fortify\UpdateUserPassword;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Bookmark;
use App\Models\Event;
use App\Models\SyncedTransaction;
use App\Models\User;
use App\Support\ImageCompressionOptions;
use App\Support\MemberNotificationFeed;
use App\Support\SafeImageCompressor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserDashboardController extends Controller
{
    private const AVATAR_IMAGE_MAX_EDGE = 640;

    private const AVATAR_IMAGE_QUALITY = 84;

    private const AVATAR_IMAGE_MAX_PIXELS = 50000000;

    private const AVATAR_UPLOAD_MAX_KILOBYTES = 51200;

    private const AVATAR_ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    public function __construct(
        private readonly MemberNotificationFeed $notificationFeed,
        private readonly SafeImageCompressor $imageCompressor,
    ) {}

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
                'source' => (string) data_get($notification->data, 'source', 'admin'),
                'action_url' => data_get($notification->data, 'action_url'),
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
                'mimetypes:'.implode(',', self::AVATAR_ALLOWED_MIME_TYPES),
                'max:'.self::AVATAR_UPLOAD_MAX_KILOBYTES,
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $accountDetails = [
            'name' => $validated['name'],
            'email' => Str::lower($validated['email']),
            'phone' => ($validated['phone'] ?? null) ?: null,
            'registration_country_code' => filled($validated['country_code'] ?? null)
                ? Str::upper((string) $validated['country_code'])
                : null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
        ];
        $originalAccountDetails = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'registration_country_code' => $user->registration_country_code,
            'date_of_birth' => $user->date_of_birth?->toDateString(),
            'gender' => $user->gender,
        ];
        $accountDetailsChanged = $accountDetails !== $originalAccountDetails;
        $emailChanged = $user->email !== $accountDetails['email'];
        $avatarUrl = null;
        $previousAvatar = $user->avatar;

        if ($request->hasFile('avatar')) {
            $avatarUrl = $this->storeCompressedAvatar($request->file('avatar'), $user);
        }

        $user->forceFill([
            ...$accountDetails,
            'avatar' => $avatarUrl ?? $user->avatar,
            'email_verified_at' => $emailChanged ? null : $user->email_verified_at,
        ])->save();

        if ($avatarUrl) {
            $this->deleteStoredAvatar($previousAvatar);
        }

        if ($accountDetailsChanged) {
            $this->notificationFeed->recordActivity(
                user: $user,
                title: 'Profile updated',
                body: 'Your account profile details were updated.',
                activityType: 'account.profile_updated',
            );
        }

        if ($avatarUrl) {
            $this->notificationFeed->recordActivity(
                user: $user,
                title: 'Profile photo updated',
                body: 'Your member profile photo was changed.',
                activityType: 'account.photo_updated',
            );
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

        $this->notificationFeed->recordActivity(
            user: $user,
            title: 'Password changed',
            body: 'Your account password was updated.',
            activityType: 'account.password_updated',
        );

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

        if ($bookmark->wasRecentlyCreated) {
            $this->notificationFeed->recordActivity(
                user: $user,
                title: 'Event saved',
                body: '"'.$event->title.'" was added to your saved events.',
                activityType: 'saved_event.created',
                actionUrl: '/events/'.$event->slug,
            );
        }

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
        abort_unless($user->hasVerifiedEmail(), 403, 'Verify your email before accessing the member dashboard.');

        return $user;
    }

    private function storeCompressedAvatar(UploadedFile $upload, User $user): string
    {
        return $this->imageCompressor->storeUploaded(
            $upload,
            new ImageCompressionOptions(
                directory: 'profile-avatars/'.$user->id,
                filenamePrefix: 'avatar',
                errorField: 'avatar',
                label: 'profile image',
                maxEdge: self::AVATAR_IMAGE_MAX_EDGE,
                quality: self::AVATAR_IMAGE_QUALITY,
                maxPixels: self::AVATAR_IMAGE_MAX_PIXELS,
                returnUrl: true,
                allowedMimeTypes: self::AVATAR_ALLOWED_MIME_TYPES,
            ),
        );
    }

    private function deleteStoredAvatar(?string $avatarUrl): void
    {
        $this->imageCompressor->deletePublicPath($avatarUrl, 'profile-avatars/');
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

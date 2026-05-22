<?php

namespace Tests\Feature;

use App\Models\Bookmark;
use App\Models\Event;
use App\Models\SyncedTransaction;
use App\Models\User;
use App\Support\MemberNotificationFeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_fetch_member_dashboard_payload(): void
    {
        Role::findOrCreate('user');

        $user = User::factory()->create([
            'email' => 'member@blacksky.test',
            'registration_country_code' => 'MY',
        ]);
        $user->assignRole('user');

        $event = $this->publishedEvent();
        Bookmark::query()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
        SyncedTransaction::query()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'vendor' => 'ticket2u',
            'external_order_id' => 'T2U-1001',
            'buyer_email' => $user->email,
            'event_title' => $event->title,
            'ticket_type' => 'General Admission',
            'quantity' => 2,
            'total_amount' => 320,
            'currency' => 'MYR',
            'status' => 'confirmed',
            'purchased_at' => now(),
        ]);

        $this->actingAs($user);

        $this->getJson('/api/v1/me/dashboard')
            ->assertOk()
            ->assertJsonPath('data.user.email', 'member@blacksky.test')
            ->assertJsonPath('data.user.country_code', 'MY')
            ->assertJsonPath('data.stats.tickets', 1)
            ->assertJsonPath('data.stats.saved_events', 1)
            ->assertJsonPath('data.tickets.0.event.slug', 'member-night-live')
            ->assertJsonPath('data.saved_events.0.event.slug', 'member-night-live');
    }

    public function test_member_dashboard_only_returns_own_notifications_with_admin_broadcasts(): void
    {
        Role::findOrCreate('admin');
        Role::findOrCreate('user');

        $user = User::factory()->create(['email' => 'member-feed@blacksky.test']);
        $otherUser = User::factory()->create(['email' => 'other-feed@blacksky.test']);
        $admin = User::factory()->create(['email' => 'admin-feed@blacksky.test']);
        $user->assignRole('user');
        $otherUser->assignRole('user');
        $admin->assignRole('admin');

        $feed = app(MemberNotificationFeed::class);
        $feed->broadcastToMembers(
            title: 'Admin broadcast',
            body: 'A new Black Sky announcement is live.',
            adminId: $admin->id,
        );
        $feed->recordActivity(
            user: $otherUser,
            title: 'Other profile updated',
            body: 'This belongs to another member.',
            activityType: 'account.profile_updated',
        );
        $feed->recordActivity(
            user: $user,
            title: 'Profile updated',
            body: 'Your account profile details were updated.',
            activityType: 'account.profile_updated',
        );

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/me/dashboard')
            ->assertOk()
            ->assertJsonPath('data.stats.unread_notifications', 2);

        $notifications = collect($response->json('data.notifications'));
        $titles = $notifications->pluck('title');

        $this->assertContains('Admin broadcast', $titles);
        $this->assertContains('Profile updated', $titles);
        $this->assertNotContains('Other profile updated', $titles);
        $this->assertSame('admin', $notifications->firstWhere('title', 'Admin broadcast')['source']);
        $this->assertSame('activity', $notifications->firstWhere('title', 'Profile updated')['source']);
    }

    public function test_unverified_user_cannot_access_member_dashboard_api(): void
    {
        Role::findOrCreate('user');

        $user = User::factory()->unverified()->create([
            'email' => 'unverified@blacksky.test',
        ]);
        $user->assignRole('user');
        $event = $this->publishedEvent();

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/me/dashboard')->assertForbidden();
        $this->patchJson('/api/v1/me/account', [
            'name' => 'Unverified Member',
            'email' => 'unverified@blacksky.test',
        ])->assertForbidden();
        $this->postJson('/api/v1/me/bookmarks/' . $event->id)->assertForbidden();
    }

    public function test_user_can_update_account_details(): void
    {
        Role::findOrCreate('user');

        $user = User::factory()->create(['email' => 'old@blacksky.test']);
        $user->assignRole('user');

        $this->actingAs($user);

        $this->patchJson('/api/v1/me/account', [
            'name' => 'Updated Member',
            'email' => 'updated@blacksky.test',
            'phone' => '+60123456789',
            'country_code' => 'id',
            'date_of_birth' => '1998-04-21',
            'gender' => 'prefer_not_to_say',
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Member')
            ->assertJsonPath('data.email', 'updated@blacksky.test')
            ->assertJsonPath('data.country_code', 'ID')
            ->assertJsonPath('data.date_of_birth', '1998-04-21')
            ->assertJsonPath('data.gender', 'prefer_not_to_say');

        $this->assertTrue($user->fresh()->notifications->contains(
            fn ($notification): bool => data_get($notification->data, 'type') === 'account.profile_updated',
        ));
    }

    public function test_user_can_upload_profile_avatar(): void
    {
        Storage::fake('public');
        Role::findOrCreate('user');

        $user = User::factory()->create(['email' => 'avatar@blacksky.test']);
        $user->assignRole('user');

        $this->actingAs($user);

        $response = $this->post('/api/v1/me/account', [
            'name' => 'Avatar Member',
            'email' => 'avatar@blacksky.test',
            'avatar' => UploadedFile::fake()->image('profile.png', 1200, 1200),
        ], [
            'Accept' => 'application/json',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'Avatar Member');

        $avatar = (string) $response->json('data.avatar');

        $this->assertStringContainsString('/storage/profile-avatars/', $avatar);
        $this->assertStringEndsWith('.webp', $avatar);
        $this->assertStringNotContainsString('profile.png', $avatar);
        Storage::disk('public')->assertExists(Str::after($avatar, '/storage/'));
        $this->assertTrue($user->fresh()->notifications->contains(
            fn ($notification): bool => data_get($notification->data, 'type') === 'account.photo_updated',
        ));
    }

    public function test_user_avatar_upload_rejects_non_image_files(): void
    {
        Storage::fake('public');
        Role::findOrCreate('user');

        $user = User::factory()->create(['email' => 'avatar-security@blacksky.test']);
        $user->assignRole('user');

        $this->actingAs($user);

        $this->post('/api/v1/me/account', [
            'name' => 'Avatar Member',
            'email' => 'avatar-security@blacksky.test',
            'avatar' => UploadedFile::fake()->create('shell.php', 8, 'application/x-php'),
        ], [
            'Accept' => 'application/json',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('avatar');

        $this->assertSame([], Storage::disk('public')->allFiles('profile-avatars'));
    }

    public function test_user_can_update_password_from_member_dashboard(): void
    {
        Role::findOrCreate('user');

        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);
        $user->assignRole('user');

        $this->actingAs($user);

        $this->patchJson('/api/v1/me/password', [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertOk()
            ->assertJsonPath('message', 'Password updated.');

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
        $this->assertTrue($user->fresh()->notifications->contains(
            fn ($notification): bool => data_get($notification->data, 'type') === 'account.password_updated',
        ));
    }

    public function test_user_can_save_and_remove_event_bookmark(): void
    {
        Role::findOrCreate('user');

        $user = User::factory()->create();
        $user->assignRole('user');
        $event = $this->publishedEvent();

        Sanctum::actingAs($user);

        $this->postJson('/api/v1/me/bookmarks/' . $event->id)
            ->assertCreated()
            ->assertJsonPath('data.event.slug', 'member-night-live');

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
        $this->assertTrue($user->fresh()->notifications->contains(
            fn ($notification): bool => data_get($notification->data, 'type') === 'saved_event.created',
        ));

        $this->deleteJson('/api/v1/me/bookmarks/' . $event->id)
            ->assertOk();

        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
    }

    public function test_admin_cannot_access_member_dashboard_api(): void
    {
        Role::findOrCreate('admin');

        $admin = User::factory()->create(['email' => 'admin@blacksky.test']);
        $admin->assignRole('admin');

        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/me/dashboard')->assertForbidden();
        $this->patchJson('/api/v1/me/account', [
            'name' => 'Admin User',
            'email' => 'admin@blacksky.test',
        ])->assertForbidden();
        $this->patchJson('/api/v1/me/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertForbidden();
    }

    public function test_user_can_remove_member_account_with_password_confirmation(): void
    {
        Role::findOrCreate('user');

        $user = User::factory()->create([
            'password' => Hash::make('secret-password'),
        ]);
        $user->assignRole('user');

        Sanctum::actingAs($user);

        $this->deleteJson('/api/v1/me/account', [
            'password' => 'secret-password',
        ])
            ->assertOk()
            ->assertJsonPath('message', 'Account removed.');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    private function publishedEvent(): Event
    {
        return Event::query()->create([
            'title' => 'Member Night Live',
            'slug' => 'member-night-live',
            'subtitle' => 'Member access show',
            'venue' => 'Axiata Arena',
            'city' => 'Kuala Lumpur',
            'country_code' => 'MY',
            'genre' => 'Concert',
            'start_date' => now()->addMonth()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'date_display' => now()->addMonth()->format('M d, Y'),
            'timezone' => 'Asia/Kuala_Lumpur',
            'status' => 'published',
            'is_sold_out' => false,
            'image_url' => 'https://example.com/event.jpg',
            'vendor_url' => 'https://example.com/tickets',
            'published_at' => now(),
        ]);
    }
}

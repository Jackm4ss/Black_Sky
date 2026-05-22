<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Database\Seeders\EventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_through_api(): void
    {
        Notification::fake();

        $this->postJson('/api/register', [
            'name' => 'Black Sky Member',
            'email' => 'member@blacksky.test',
            'country_code' => 'MY',
            'registration_source' => 'tiktok',
            'registration_referrer' => 'https://www.tiktok.com/@blacksky',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'terms' => true,
        ])->assertCreated();

        $user = User::query()->where('email', 'member@blacksky.test')->firstOrFail();

        $this->assertTrue($user->hasRole('user'));
        $this->assertNull($user->email_verified_at);
        $this->assertSame('MY', $user->registration_country_code);
        $this->assertSame('tiktok', $user->registration_source);
        $this->assertSame('https://www.tiktok.com/@blacksky', $user->registration_referrer);

        Notification::assertSentTo($user, VerifyEmail::class);

        $this->getJson('/api/v1/me/dashboard')->assertForbidden();
    }

    public function test_verify_email_notification_uses_black_sky_email_template(): void
    {
        Role::findOrCreate('user');

        $user = User::factory()->unverified()->create([
            'name' => 'Mailer Member',
            'email' => 'mailer-member@blacksky.test',
        ]);
        $user->assignRole('user');

        $mail = (new VerifyEmail)->toMail($user);
        $html = (string) $mail->render();

        $this->assertSame('Verify your Black Sky account', $mail->subject);
        $this->assertStringContainsString('images/black-sky-logo.png', $html);
        $this->assertStringContainsString('alt="Black Sky"', $html);
        $this->assertStringContainsString('Welcome to Black Sky', $html);
        $this->assertStringContainsString('api/verify-email/' . $user->id, $html);
        $this->assertStringContainsString('Verify Email Address', $html);
    }

    public function test_verification_email_resend_is_rate_limited(): void
    {
        Notification::fake();
        Role::findOrCreate('user');

        $user = User::factory()->unverified()->create([
            'email' => 'resend-cooldown@blacksky.test',
        ]);
        $user->assignRole('user');

        $this->actingAs($user)
            ->postJson('/api/email/verification-notification')
            ->assertAccepted();

        $response = $this->actingAs($user)
            ->postJson('/api/email/verification-notification')
            ->assertTooManyRequests()
            ->assertJsonPath(
                'message',
                'Please wait before requesting another verification email.',
            );

        $retryAfter = (int) $response->headers->get('Retry-After');

        $this->assertGreaterThan(0, $retryAfter);
        $this->assertLessThanOrEqual(35, $retryAfter);
        Notification::assertSentToTimes($user, VerifyEmail::class, 1);
    }

    public function test_verification_link_marks_user_email_verified_without_login(): void
    {
        Role::findOrCreate('user');

        $user = User::factory()->unverified()->create([
            'email' => 'verify-link@blacksky.test',
        ]);
        $user->assignRole('user');

        $verificationUrl = URL::temporarySignedRoute(
            'api.verify-email',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ],
        );

        $this->assertGuest();

        $this->get($verificationUrl)
            ->assertRedirect('/dashboard');

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->assertGuest();
    }

    public function test_user_can_login_and_fetch_current_user(): void
    {
        Role::findOrCreate('user');

        $user = User::factory()->create([
            'email' => 'member@blacksky.test',
            'password' => Hash::make('Password123!'),
            'is_active' => true,
        ]);

        $user->assignRole('user');

        $this->postJson('/api/login', [
            'email' => 'member@blacksky.test',
            'password' => 'Password123!',
        ])->assertOk();

        $this->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('data.email', 'member@blacksky.test')
            ->assertJsonPath('data.roles.0', 'user');
    }

    public function test_unverified_user_cannot_login_through_member_login_api(): void
    {
        Role::findOrCreate('user');

        $user = User::factory()->unverified()->create([
            'email' => 'unverified-login@blacksky.test',
            'password' => Hash::make('Password123!'),
            'is_active' => true,
        ]);
        $user->assignRole('user');

        $this->postJson('/api/login', [
            'email' => 'unverified-login@blacksky.test',
            'password' => 'Password123!',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email')
            ->assertJsonPath('errors.email.0', 'Please verify your email before signing in.');

        $this->assertGuest();
    }

    public function test_admin_cannot_login_through_member_login_api(): void
    {
        Role::findOrCreate('admin');

        $admin = User::factory()->create([
            'email' => 'admin@blacksky.test',
            'password' => Hash::make('Password123!'),
            'is_active' => true,
        ]);

        $admin->assignRole('admin');

        $this->postJson('/api/login', [
            'email' => 'admin@blacksky.test',
            'password' => 'Password123!',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email')
            ->assertJsonPath('errors.email.0', 'These credentials do not match our records.');

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'inactive@blacksky.test',
            'password' => Hash::make('Password123!'),
            'is_active' => false,
        ]);

        $this->postJson('/api/login', [
            'email' => 'inactive@blacksky.test',
            'password' => 'Password123!',
        ])->assertUnprocessable();
    }

    public function test_authenticated_user_endpoint_returns_user_payload(): void
    {
        Role::findOrCreate('user');

        $user = User::factory()->create([
            'email' => 'member@blacksky.test',
        ]);

        $user->assignRole('user');
        Sanctum::actingAs($user);

        $this->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('data.email', 'member@blacksky.test')
            ->assertJsonPath('data.roles.0', 'user');
    }

    public function test_health_endpoint_returns_ok(): void
    {
        $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('service', 'Black Sky');
    }

    public function test_public_events_endpoint_returns_event_list(): void
    {
        $this->seed(EventSeeder::class);

        $this->getJson('/api/v1/events')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'ignite-festival')
            ->assertJsonPath('data.0.accent_color', '#A855F7')
            ->assertJsonPath('meta.per_page', 12)
            ->assertJsonPath('meta.filters.cities.0', 'Kuala Lumpur');
    }

    public function test_public_events_endpoint_filters_discovery_catalog(): void
    {
        Event::query()->create([
            'title' => 'ARCHIVE NIGHT',
            'slug' => 'archive-night',
            'subtitle' => 'A past Black Sky showcase',
            'venue' => 'Zepp KL',
            'city' => 'Kuala Lumpur',
            'country_code' => 'MY',
            'genre' => 'CONCERT',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->subMonth()->toDateString(),
            'status' => 'published',
            'is_sold_out' => false,
            'image_url' => 'https://example.com/archive.jpg',
            'accent_color' => '#0EA5E9',
            'timezone' => 'Asia/Kuala_Lumpur',
            'published_at' => now()->subMonths(2),
        ]);

        Event::query()->create([
            'title' => 'FUTURE NIGHT',
            'slug' => 'future-night',
            'subtitle' => 'An upcoming Black Sky showcase',
            'venue' => 'Axiata Arena',
            'city' => 'Kuala Lumpur',
            'country_code' => 'MY',
            'genre' => 'CONCERT',
            'start_date' => now()->addMonth()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'status' => 'published',
            'is_sold_out' => true,
            'image_url' => 'https://example.com/future.jpg',
            'accent_color' => '#A855F7',
            'timezone' => 'Asia/Kuala_Lumpur',
            'published_at' => now()->subDay(),
        ]);

        $this->getJson('/api/v1/events?timeframe=past')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'archive-night');

        $this->getJson('/api/v1/events?timeframe=upcoming&availability=sold_out')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'future-night');
    }

    public function test_discover_page_has_indexable_meta(): void
    {
        $this->withoutVite();

        $this->get('/discover')
            ->assertOk()
            ->assertSee('Discover Events | Black Sky Enterprise')
            ->assertSee('CollectionPage');
    }
}

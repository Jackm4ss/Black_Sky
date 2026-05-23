<?php

namespace Tests\Feature;

use App\Filament\Pages\Events\ListEvents;
use App\Models\Event;
use App\Models\User;
use Database\Seeders\EventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_filament_admin_login(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_login_page_uses_brand_logo_and_production_subheading(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('images/black-sky-logo.png', false)
            ->assertSee('Manage Black Sky events, articles, registrations, and portfolio work from one secure admin area.');
    }

    public function test_admin_can_access_custom_filament_dashboard(): void
    {
        Role::findOrCreate('admin');

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Analytics Dashboard')
            ->assertSee('Total Event')
            ->assertSee('Registration Traffic Sources')
            ->assertSee('Registration Based on Top Countries')
            ->assertSee('No registration sources yet')
            ->assertSee('No country data yet')
            ->assertDontSee('bsa-topbar-search', false)
            ->assertDontSee('Malaysia')
            ->assertDontSee('Indonesia');
    }

    public function test_admin_dashboard_shows_registration_attribution_data(): void
    {
        Role::findOrCreate('admin');

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        User::factory()->create([
            'registration_source' => 'tiktok',
            'registration_country_code' => 'MY',
        ]);
        User::factory()->create([
            'registration_source' => 'facebook',
            'registration_country_code' => 'ID',
        ]);
        User::factory()->create([
            'registration_source' => 'tiktok',
            'registration_country_code' => 'MY',
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Total Registered')
            ->assertSee('3')
            ->assertSee('TikTok')
            ->assertSee('Facebook')
            ->assertSee('Malaysia')
            ->assertSee('Indonesia')
            ->assertDontSee('No registration sources yet')
            ->assertDontSee('No country data yet');
    }

    public function test_user_management_list_excludes_admin_accounts(): void
    {
        Role::findOrCreate('admin');
        Role::findOrCreate('user');

        $admin = User::factory()->create([
            'email' => 'cms-admin@blacksky.test',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        $member = User::factory()->create([
            'email' => 'member-list@blacksky.test',
            'is_active' => true,
        ]);
        $member->assignRole('user');

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertOk()
            ->assertSee('member-list@blacksky.test')
            ->assertDontSee('cms-admin@blacksky.test');
    }

    public function test_admin_can_access_events_list_view(): void
    {
        Role::findOrCreate('admin');
        $this->seed(EventSeeder::class);

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/events')
            ->assertOk()
            ->assertSee('Events List View')
            ->assertSee('Manage all events and concerts')
            ->assertSee('Upcoming Events')
            ->assertSee('IGNITE FESTIVAL')
            ->assertSee('ECHOES')
            ->assertSee('Search events')
            ->assertSee('Date Range')
            ->assertDontSee('Country ISO');
    }

    public function test_events_list_view_filters_by_search_and_date_range(): void
    {
        Role::findOrCreate('admin');
        $this->seed(EventSeeder::class);

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/events?search=echo')
            ->assertOk()
            ->assertSee('ECHOES')
            ->assertDontSee('IGNITE FESTIVAL');

        $this->actingAs($admin)
            ->get('/admin/events?from=2026-09-01&to=2026-12-31')
            ->assertOk()
            ->assertSee('SKY SESSIONS')
            ->assertSee('STARLIGHT')
            ->assertDontSee('ECHOES');
    }

    public function test_admin_can_create_edit_and_delete_event_from_events_page(): void
    {
        Role::findOrCreate('admin');

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $this->actingAs($admin);
        Storage::fake('public');

        Livewire::test(ListEvents::class)
            ->call('openCreate')
            ->set('form.title', 'NEON TEST')
            ->set('form.subtitle', 'Production-ready event test')
            ->set('form.venue', 'Axiata Arena')
            ->set('form.city', 'Kuala Lumpur')
            ->set('form.country_code', 'MY')
            ->set('form.genre', 'FESTIVAL')
            ->set('form.start_date', '2026-11-15')
            ->set('form.end_date', '2026-11-15')
            ->set('form.status', 'published')
            ->set('eventImage', UploadedFile::fake()->image('event-poster.jpg', 3840, 2160)->size(8192))
            ->set('seatMapImage', UploadedFile::fake()->image('seat-map.jpg', 4096, 2160)->size(8192))
            ->set('form.accent_color', '#0EA5E9')
            ->call('saveEvent')
            ->assertSet('isFormOpen', false);

        $this->assertDatabaseHas('events', [
            'title' => 'NEON TEST',
            'slug' => 'neon-test',
            'status' => 'published',
        ]);

        $event = Event::query()->where('slug', 'neon-test')->firstOrFail();
        $this->assertStringContainsString('/storage/events/neon-test-', $event->image_url);
        $this->assertStringEndsWith('.webp', $event->image_url);
        $eventImagePath = Str::after(parse_url($event->image_url, PHP_URL_PATH), '/storage/');
        $seatMapImagePath = Str::after(parse_url(
            $event->sections()->where('section_key', 'seat_map_ticket_pricing')->value('image_url'),
            PHP_URL_PATH
        ), '/storage/');
        Storage::disk('public')->assertExists($eventImagePath);
        Storage::disk('public')->assertExists($seatMapImagePath);

        Livewire::test(ListEvents::class)
            ->call('openEdit', $event->id)
            ->set('form.title', 'NEON TEST UPDATED')
            ->set('form.slug', 'neon-test-updated')
            ->set('form.status', 'draft')
            ->call('saveEvent')
            ->assertSet('isFormOpen', false);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'NEON TEST UPDATED',
            'slug' => 'neon-test-updated',
            'status' => 'draft',
            'published_at' => null,
        ]);

        Livewire::test(ListEvents::class)
            ->call('confirmDelete', $event->id)
            ->assertSet('isDeleteOpen', true)
            ->call('deleteEvent')
            ->assertSet('isDeleteOpen', false);

        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
        ]);
        Storage::disk('public')->assertMissing($eventImagePath);
        Storage::disk('public')->assertMissing($seatMapImagePath);
    }

    public function test_regular_user_cannot_access_filament_dashboard(): void
    {
        Role::findOrCreate('user');

        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('user');

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_regular_user_cannot_access_events_list_view(): void
    {
        Role::findOrCreate('user');

        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('user');

        $this->actingAs($user)
            ->get('/admin/events')
            ->assertForbidden();
    }
}

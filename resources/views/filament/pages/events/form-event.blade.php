<x-filament-panels::page class="bsa-events-page bsa-events-form-page">
    <div class="bsa-events">
        <section class="bsa-events-hero">
            <div>
                <p class="bsa-eyebrow">{{ $editingEventId ? 'Edit Event' : 'Create Event' }}</p>
                <h1>{{ $editingEventId ? 'Update Event' : 'Add New Event' }}</h1>
                <p class="bsa-muted">Event card, schedule, ticket status, detail page, and publishing settings.</p>
            </div>

            <a class="bsa-events-modal-secondary bsa-events-back-link" href="{{ \App\Filament\Pages\Events\ListEvents::getUrl() }}">
                <x-heroicon-o-arrow-left />
                <span>Events</span>
            </a>
        </section>

        <section class="bsa-events-card bsa-events-form-card" aria-label="{{ $editingEventId ? 'Edit event form' : 'Create event form' }}">
            <div class="bsa-events-card-head">
                <div>
                    <p class="bsa-eyebrow">Event Setup</p>
                    <h2>{{ $editingEventId ? 'Edit Event' : 'Create Event' }}</h2>
                </div>
            </div>

            @include('filament.pages.events.partials.event-form')
        </section>
    </div>
</x-filament-panels::page>

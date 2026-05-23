<form class="bsa-events-form" wire:submit.prevent="saveEvent" enctype="multipart/form-data">
    <section class="bsa-events-form-panel">
        <header class="bsa-events-form-panel-head">
            <span>Event Card</span>
        </header>

        <div class="bsa-events-form-grid bsa-events-form-grid-simple">
            <label class="bsa-events-field">
                <span>Event Name<sup class="bsa-events-required-mark" aria-hidden="true">*</sup></span>
                <input id="event-form-title-input" name="title" type="text" wire:model="form.title" placeholder="IGNITE FESTIVAL">
                @error('form.title') <em>{{ $message }}</em> @enderror
            </label>

            <label class="bsa-events-field">
                <span>Genre<sup class="bsa-events-required-mark" aria-hidden="true">*</sup></span>
                <select id="event-form-genre" name="genre" wire:model="form.genre">
                    <option value="">Select genre</option>
                    @foreach ($this->genreOptions() as $genreValue => $genreLabel)
                        <option value="{{ $genreValue }}">{{ $genreLabel }}</option>
                    @endforeach
                </select>
                @error('form.genre') <em>{{ $message }}</em> @enderror
            </label>

            <label class="bsa-events-field bsa-events-field-wide">
                <span>About</span>
                <input id="event-form-subtitle" name="subtitle" type="text" wire:model="form.subtitle" placeholder="The Greatest Electronic Music Gathering in SEA">
                @error('form.subtitle') <em>{{ $message }}</em> @enderror
            </label>

            <div class="bsa-events-upload bsa-events-field-wide">
                <label class="bsa-events-field">
                    <span>
                        Artwork
                        @if (! $editingEventId)
                            <sup class="bsa-events-required-mark" aria-hidden="true">*</sup>
                        @endif
                    </span>
                    <input
                        id="event-form-image-file"
                        name="event_image"
                        type="file"
                        wire:model="eventImage"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                    >
                    @error('eventImage') <em>{{ $message }}</em> @enderror
                </label>

                <div class="bsa-events-upload-preview">
                    @if ($eventImage)
                        <img src="{{ $eventImage->temporaryUrl() }}" alt="Selected event artwork preview">
                        <span>Selected artwork</span>
                    @elseif (filled($form['image_url'] ?? null))
                        <img src="{{ $form['image_url'] }}" alt="Current event artwork preview">
                        <span>Current artwork</span>
                    @else
                        <div>
                            <x-heroicon-o-photo />
                            <span>JPG, PNG, WEBP, GIF</span>
                        </div>
                    @endif
                </div>
            </div>

            <label class="bsa-events-field">
                <span>Accent Color<sup class="bsa-events-required-mark" aria-hidden="true">*</sup></span>
                <input id="event-form-accent" name="accent_color" type="color" wire:model="form.accent_color">
                @error('form.accent_color') <em>{{ $message }}</em> @enderror
            </label>
        </div>
    </section>

    <section class="bsa-events-form-panel">
        <header class="bsa-events-form-panel-head">
            <span>Schedule & Venue</span>
        </header>

        <div class="bsa-events-form-grid">
            <div class="bsa-events-field bsa-events-field-wide">
                <span>Event Date Range<sup class="bsa-events-required-mark" aria-hidden="true">*</sup></span>
                <div
                    wire:ignore
                    data-bsa-date-range-picker
                    data-from-input="event-form-start-date"
                    data-to-input="event-form-end-date"
                ></div>
                <input id="event-form-start-date" name="start_date" type="hidden" wire:model.live="form.start_date" value="{{ $form['start_date'] ?? '' }}">
                <input id="event-form-end-date" name="end_date" type="hidden" wire:model.live="form.end_date" value="{{ $form['end_date'] ?? '' }}">
                @error('form.start_date') <em>{{ $message }}</em> @enderror
                @error('form.end_date') <em>{{ $message }}</em> @enderror
            </div>

            <div class="bsa-events-field">
                <span>Start Time</span>
                <div
                    wire:ignore
                    data-bsa-time-picker
                    data-time-input="event-form-start-time"
                ></div>
                <input id="event-form-start-time" name="start_time" type="hidden" wire:model="form.start_time" value="{{ $form['start_time'] ?? '' }}">
                @error('form.start_time') <em>{{ $message }}</em> @enderror
            </div>

            <label class="bsa-events-field">
                <span>Venue<sup class="bsa-events-required-mark" aria-hidden="true">*</sup></span>
                <input id="event-form-venue" name="venue" type="text" wire:model="form.venue" placeholder="Venue name">
                @error('form.venue') <em>{{ $message }}</em> @enderror
            </label>

            <label class="bsa-events-field">
                <span>City<sup class="bsa-events-required-mark" aria-hidden="true">*</sup></span>
                <input id="event-form-city" name="city" type="text" wire:model="form.city" placeholder="Kuala Lumpur">
                @error('form.city') <em>{{ $message }}</em> @enderror
            </label>

            <div class="bsa-events-field">
                <span>Country<sup class="bsa-events-required-mark" aria-hidden="true">*</sup></span>
                <div
                    wire:ignore
                    data-bsa-country-dropdown
                    data-country-input="event-form-country"
                ></div>
                <input id="event-form-country" name="country_code" type="hidden" wire:model.live="form.country_code" value="{{ $form['country_code'] ?? '' }}">
                @error('form.country_code') <em>{{ $message }}</em> @enderror
            </div>
        </div>
    </section>

    <section class="bsa-events-form-panel">
        <header class="bsa-events-form-panel-head">
            <span>Tickets & Status</span>
        </header>

        <div class="bsa-events-form-grid bsa-events-form-grid-simple">
            <label class="bsa-events-field">
                <span>Ticket Link</span>
                <input id="event-form-vendor" name="vendor_url" type="url" wire:model="form.vendor_url" placeholder="https://...">
                @error('form.vendor_url') <em>{{ $message }}</em> @enderror
            </label>

            <label class="bsa-events-field">
                <span>Visibility<sup class="bsa-events-required-mark" aria-hidden="true">*</sup></span>
                <select id="event-form-status" name="status" wire:model="form.status">
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                    <option value="archived">Archived</option>
                </select>
                @error('form.status') <em>{{ $message }}</em> @enderror
            </label>

            <label class="bsa-events-toggle bsa-events-field-wide">
                <input id="event-form-sold-out" name="is_sold_out" type="checkbox" wire:model="form.is_sold_out">
                <span>Sold Out</span>
            </label>
        </div>
    </section>

    <section class="bsa-events-form-panel bsa-events-content-builder">
        <header class="bsa-events-form-panel-head">
            <span>Detail Page</span>
        </header>

        <div class="bsa-events-detail-grid">
            <label class="bsa-events-field bsa-events-field-wide">
                <span>Spotify Preview URL</span>
                <input
                    id="event-form-spotify"
                    name="spotify_embed_url"
                    type="url"
                    wire:model="form.spotify_embed_url"
                    placeholder="https://open.spotify.com/artist/..."
                >
                @error('form.spotify_embed_url') <em>{{ $message }}</em> @enderror
            </label>

            <label class="bsa-events-field bsa-events-field-wide">
                <span>Event Details</span>
                <textarea
                    id="event-form-detail-event-details"
                    name="detail_event_details"
                    rows="4"
                    wire:model="form.detail_event_details"
                    placeholder="Show notes, door open information, or event flow."
                ></textarea>
                @error('form.detail_event_details') <em>{{ $message }}</em> @enderror
            </label>

            <label class="bsa-events-field bsa-events-field-wide">
                <span>Google Maps Link</span>
                <input
                    id="event-form-google-maps"
                    name="google_maps_url"
                    type="url"
                    wire:model="form.google_maps_url"
                    placeholder="https://maps.app.goo.gl/..."
                >
                @error('form.google_maps_url') <em>{{ $message }}</em> @enderror
            </label>

            <div class="bsa-events-upload bsa-events-field-wide">
                <label class="bsa-events-field">
                    <span>Seat Map & Ticket Pricing Image</span>
                    <input
                        id="event-form-seat-map-file"
                        name="seat_map_image"
                        type="file"
                        wire:model="seatMapImage"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                    >
                    @error('seatMapImage') <em>{{ $message }}</em> @enderror
                </label>

                <div class="bsa-events-upload-preview">
                    @if ($seatMapImage)
                        <img src="{{ $seatMapImage->temporaryUrl() }}" alt="Selected seat map preview">
                        <span>Selected guide image</span>
                    @elseif (filled($form['seat_map_image_url'] ?? null))
                        <img src="{{ $form['seat_map_image_url'] }}" alt="Current seat map preview">
                        <span>Current guide image</span>
                    @else
                        <div>
                            <x-heroicon-o-map />
                            <span>JPG, PNG, WEBP, GIF</span>
                        </div>
                    @endif
                </div>
            </div>

            <label class="bsa-events-field bsa-events-field-wide">
                <span>Ticket Pricing</span>
                <textarea
                    id="event-form-ticket-pricing"
                    name="ticket_pricing"
                    rows="4"
                    wire:model="form.ticket_pricing"
                    placeholder="- General Admission: RM 188&#10;- Premium Zone: RM 328&#10;- VIP Deck: RM 488"
                ></textarea>
                @error('form.ticket_pricing') <em>{{ $message }}</em> @enderror
            </label>
        </div>
    </section>

    <footer class="bsa-events-modal-footer">
        <a class="bsa-events-modal-secondary" href="{{ \App\Filament\Pages\Events\ListEvents::getUrl() }}">Cancel</a>
        <button type="submit" class="bsa-events-modal-primary" wire:loading.attr="disabled" wire:target="saveEvent">
            <x-heroicon-o-check />
            <span>{{ $editingEventId ? 'Save Changes' : 'Create Event' }}</span>
        </button>
    </footer>
</form>

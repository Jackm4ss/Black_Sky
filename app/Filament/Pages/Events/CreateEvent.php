<?php

namespace App\Filament\Pages\Events;

use App\Models\Event;
use Illuminate\Contracts\Support\Htmlable;

class CreateEvent extends ListEvents
{
    protected static ?string $slug = 'events/create';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.events.form-event';

    public function getTitle(): string | Htmlable
    {
        return 'Add Event';
    }

    protected function afterEventSaved(Event $event): mixed
    {
        return redirect()->to(ListEvents::getUrl());
    }
}

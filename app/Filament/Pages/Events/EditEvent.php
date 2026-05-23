<?php

namespace App\Filament\Pages\Events;

use App\Models\Event;
use Illuminate\Contracts\Support\Htmlable;

class EditEvent extends ListEvents
{
    protected static ?string $slug = 'events/{record}/edit';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.events.form-event';

    public function mount(int | string | null $record = null): void
    {
        $this->openEdit((int) $record);
    }

    public function getTitle(): string | Htmlable
    {
        return 'Edit Event';
    }

    protected function afterEventSaved(Event $event): mixed
    {
        return redirect()->to(ListEvents::getUrl());
    }
}

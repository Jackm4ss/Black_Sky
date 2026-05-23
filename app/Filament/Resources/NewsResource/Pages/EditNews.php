<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNews extends EditRecord
{
    protected static string $resource = NewsResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['published_time'] = $this->record->published_at?->format('H:i');
        $data['scheduled_time'] = $this->record->scheduled_at?->format('H:i');

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return NewsResource::normalizeFormData($data, $this->record);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
            Actions\DeleteAction::make(),
        ];
    }
}

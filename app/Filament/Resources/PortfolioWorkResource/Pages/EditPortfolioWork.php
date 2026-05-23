<?php

namespace App\Filament\Resources\PortfolioWorkResource\Pages;

use App\Filament\Resources\PortfolioWorkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPortfolioWork extends EditRecord
{
    protected static string $resource = PortfolioWorkResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['published_time'] = $this->record->published_at?->format('H:i');

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return PortfolioWorkResource::normalizeFormData($data, $this->record);
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

<?php

namespace App\Filament\Resources\PortfolioWorkResource\Pages;

use App\Filament\Resources\PortfolioWorkResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePortfolioWork extends CreateRecord
{
    protected static string $resource = PortfolioWorkResource::class;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = PortfolioWorkResource::normalizeFormData($data);
        $data['created_by'] = Auth::id();

        return $data;
    }
}

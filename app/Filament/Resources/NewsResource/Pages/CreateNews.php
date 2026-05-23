<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = NewsResource::normalizeFormData($data);
        $data['created_by'] = Auth::id();

        return $data;
    }
}

<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use App\Models\BlogPost;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListNews extends ListRecords
{
    protected static string $resource = NewsResource::class;

    protected static string $view = 'filament.resources.list-records-shell';

    public function getHeading(): string | Htmlable
    {
        return '';
    }

    /**
     * @return array<string, mixed>
     */
    public function adminListMeta(): array
    {
        return [
            'heroEyebrow' => 'News Management',
            'heroTitle' => 'News List View',
            'heroDescription' => 'Manage public updates shown across News & Media and article detail pages.',
            'totalLabel' => 'Total News',
            'totalValue' => BlogPost::query()->count(),
            'cardEyebrow' => 'News & Media',
            'cardTitle' => 'Published News Catalog',
            'action' => [
                'type' => 'link',
                'label' => 'Add News',
                'url' => NewsResource::getUrl('create'),
                'icon' => 'heroicon-o-plus',
            ],
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

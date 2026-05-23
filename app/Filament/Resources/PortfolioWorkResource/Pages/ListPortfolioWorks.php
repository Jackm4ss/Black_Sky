<?php

namespace App\Filament\Resources\PortfolioWorkResource\Pages;

use App\Filament\Resources\PortfolioWorkResource;
use App\Models\PortfolioWork;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListPortfolioWorks extends ListRecords
{
    protected static string $resource = PortfolioWorkResource::class;

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
            'heroEyebrow' => 'Portfolio Management',
            'heroTitle' => 'Portfolio List View',
            'heroDescription' => 'Manage selected works shown across the public portfolio and landing page.',
            'totalLabel' => 'Total Projects',
            'totalValue' => PortfolioWork::query()->count(),
            'cardEyebrow' => 'Selected Works',
            'cardTitle' => 'Published Portfolio Catalog',
            'action' => [
                'type' => 'link',
                'label' => 'Add Portfolio',
                'url' => PortfolioWorkResource::getUrl('create'),
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

@php
    $resourceSlug = str_replace('/', '-', $this->getResource()::getSlug());
    $meta = method_exists($this, 'adminListMeta') ? $this->adminListMeta() : [];
    $action = $meta['action'] ?? null;
@endphp

<x-filament-panels::page
    @class([
        'fi-resource-list-records-page',
        'fi-resource-' . $resourceSlug,
        'bsa-resource-list-records-page',
    ])
>
    <div class="bsa-events bsa-resource-list-shell">
        <section class="bsa-events-hero">
            <div>
                <p class="bsa-eyebrow">{{ $meta['heroEyebrow'] ?? 'Content Management' }}</p>
                <h1>{{ $meta['heroTitle'] ?? $this->getTitle() }}</h1>
                @if (filled($meta['heroDescription'] ?? null))
                    <p class="bsa-muted">{{ $meta['heroDescription'] }}</p>
                @endif
            </div>

            @if (array_key_exists('totalValue', $meta))
                <div class="bsa-events-total">
                    <span>{{ $meta['totalLabel'] ?? 'Total Records' }}</span>
                    <strong>{{ number_format((int) $meta['totalValue']) }}</strong>
                </div>
            @endif
        </section>

        <section class="bsa-events-card bsa-resource-list-card" aria-label="{{ $meta['cardTitle'] ?? 'Records list' }}">
            <div class="bsa-events-card-head">
                <div>
                    <p class="bsa-eyebrow">{{ $meta['cardEyebrow'] ?? 'Records' }}</p>
                    <h2>{{ $meta['cardTitle'] ?? $this->getTitle() }}</h2>
                </div>

                @if ($action)
                    @if (($action['type'] ?? 'link') === 'wire')
                        <button type="button" class="bsa-events-add" wire:click="{{ $action['wireClick'] }}">
                            @if (filled($action['icon'] ?? null))
                                <x-dynamic-component :component="$action['icon']" />
                            @endif
                            <span>{{ $action['label'] }}</span>
                        </button>
                    @else
                        <a class="bsa-events-add" href="{{ $action['url'] }}">
                            @if (filled($action['icon'] ?? null))
                                <x-dynamic-component :component="$action['icon']" />
                            @endif
                            <span>{{ $action['label'] }}</span>
                        </a>
                    @endif
                @endif
            </div>

            <div class="bsa-resource-list-table">
                <x-filament-panels::resources.tabs />

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE, scopes: $this->getRenderHookScopes()) }}

                {{ $this->table }}

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER, scopes: $this->getRenderHookScopes()) }}
            </div>
        </section>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>

@php
    $state = $getState();
    $meta = \App\Support\RegistrationSourceMeta::for($state);
@endphp

@if (filled($state))
    <span class="bsa-table-source">
        <x-bsa.source-icon
            :icon="$meta['icon']"
            :color="$meta['color']"
            :label="$meta['label']"
            compact
        />
        <span>{{ $meta['label'] }}</span>
    </span>
@else
    <span class="bsa-table-source bsa-table-source--empty">
        <x-bsa.source-icon
            icon="heroicon-o-minus-circle"
            color="#94A3B8"
            label="No attribution"
            compact
        />
        <span>No attribution</span>
    </span>
@endif

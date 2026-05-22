@php
    $state = $getState();
    $code = filled($state) ? \Illuminate\Support\Str::upper((string) $state) : null;
@endphp

@if ($code)
    <span class="bsa-table-country">
        <span
            class="bsa-country-flag bsa-country-flag--compact"
            data-bsa-country-code="{{ strtolower($code) }}"
            role="img"
            aria-label="{{ $code }} flag"
        ></span>
    </span>
@else
    <span class="bsa-table-country bsa-table-country--empty">
        <span>-</span>
    </span>
@endif

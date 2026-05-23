@php
    $record = $getRecord();
    $title = trim((string) ($record->title ?? 'Untitled project')) ?: 'Untitled project';
    $excerpt = trim(strip_tags((string) ($record->excerpt ?? '')));
    $imageUrl = $record->featured_image_url ?? null;
@endphp

<span class="bsa-resource-product">
    <span class="bsa-resource-product__media" aria-hidden="true">
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" alt="" loading="lazy">
        @endif
    </span>
    <span class="bsa-resource-product__copy">
        <strong>{{ $title }}</strong>
        @if ($excerpt !== '')
            <span>{{ $excerpt }}</span>
        @endif
    </span>
</span>

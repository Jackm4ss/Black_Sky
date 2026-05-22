@props([
    'icon',
    'color' => '#94A3B8',
    'label' => 'Traffic source',
    'compact' => false,
])

@php
    $iconName = trim((string) $icon) ?: 'heroicon-o-question-mark-circle';
    $iconStyle = str_starts_with($iconName, 'si-') ? 'fill' : 'stroke';
@endphp

<span
    style="--source-color: {{ $color }}; {{ $attributes->get('style') }}"
    {{ $attributes
        ->except('style')
        ->class([
            'bsa-source-icon',
            'bsa-source-icon--compact' => $compact,
        ])
        ->merge([
            'role' => 'img',
            'aria-label' => $label,
            'data-icon-style' => $iconStyle,
        ]) }}
>
    <x-dynamic-component :component="$iconName" aria-hidden="true" focusable="false" />
</span>

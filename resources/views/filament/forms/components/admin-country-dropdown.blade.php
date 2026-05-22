<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="bsa-admin-country-field">
        <div
            wire:ignore
            data-bsa-country-dropdown
            data-country-input="{{ $getId() }}"
        ></div>
        <input
            id="{{ $getId() }}"
            type="hidden"
            {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
        >
    </div>
</x-dynamic-component>

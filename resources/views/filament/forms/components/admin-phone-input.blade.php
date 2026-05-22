<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="bsa-admin-phone-field">
        <div
            wire:ignore
            data-bsa-phone-input
            data-country-input="{{ $countryInputId }}"
            data-phone-input="{{ $getId() }}"
        ></div>
        <input
            id="{{ $getId() }}"
            type="hidden"
            {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
        >
    </div>
</x-dynamic-component>

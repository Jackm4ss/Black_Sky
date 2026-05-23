<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="bsa-admin-time-field">
        <div
            wire:ignore
            data-bsa-time-picker
            data-time-input="{{ $getId() }}"
            data-time-empty-label="{{ $emptyLabel ?? 'Pick time' }}"
            data-time-aria-label="{{ $ariaLabel ?? 'Select time' }}"
            data-time-title="{{ $dialogTitle ?? 'Select time' }}"
            data-time-description="{{ $dialogDescription ?? 'Choose the hour and minute.' }}"
        ></div>
        <input
            id="{{ $getId() }}"
            type="hidden"
            {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
        >
    </div>
</x-dynamic-component>

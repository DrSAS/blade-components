@props([
    'name',
    'label',
    'description',
])

@aware(['idPrefix', 'disableSelect'])

<div class="flex flex-col gap-2">
    <label for="{{ $idPrefix }}{{ $name }}" class="flex text-sm font-semibold text-gray-700 dark:text-white">
        {{ $label }}

        @if (isset($attributes['required']))
            <x-blade-components::popover>{{ __("Ce champ est requis") }}</x-popover>
        @endif

    </label>

    @if (!empty($description))
        <p id="{{ $idPrefix }}text-{{ $attributes->get('name') }}" class="text-xs font-normal text-gray-500 dark:text-gray-300">{{ $description }}</p>
    @endif

    {{-- TODO : Refactoriser l'injection de classe : c'est pas intuitif quand on veut surcharger l'input + le container --}}
    <select
        {{ $attributes->filter(fn ($value, $key) => !in_array($key, ['id', 'name'])) }}
        type="text"
        id="{{ $idPrefix }}{{ $name }}"
        name="{{ $name }}"
        {{ $disableSelect }}
        @if (!empty($description)) aria-describedby="{{ $idPrefix }}text-{{ $attributes->get('name') }}" @endif
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 overflow-auto"
    >{{ $slot }}</select>
</div>
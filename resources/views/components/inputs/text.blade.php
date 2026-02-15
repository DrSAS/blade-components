@aware(['idPrefix', 'value'])

@props([
    'label',
    'description',
    'descriptionPosition',
    'value',
    'icon' => false,
])

<div class="flex flex-col gap-2 justify-between w-full">
    <label for="{{ $idPrefix }}{{ $attributes->get('name') }}" class="text-sm font-semibold text-gray-700 dark:text-white">
        {{ $label }}
        {{ (isset($attributes['required'])) ? ' *' : '' }}
    </label>
    <div @class([
        "flex gap-[inherit] flex-col",
        "flex-col-reverse!" => (isset($descriptionPosition) && $descriptionPosition === "after"),
    ])>
        @if (!empty($description))
            <p id="{{ $idPrefix }}text-{{ $attributes->get('name') }}" class="text-xs font-normal text-gray-500 dark:text-gray-300">{{ $description }}</p>
        @endif
        {{-- TODO : Refactoriser l'injection de classe : c'est pas intuitif quand on veut surcharger l'input + le container --}}
        @if ($icon)
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="{{ $icon }}"></i>
            </div>
        @endif
            <input
            {{ $attributes->filter(fn ($value, $key) => !in_array($key, ['class'])) }}
            type="text"
            @if (!empty($description)) aria-describedby="{{ $idPrefix }}text-{{ $attributes->get('name') }}" @endif
            id="{{ $idPrefix }}{{ $attributes->get('name') }}"
            value="{{ $value }}"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 {{ $icon ? 'pl-10' : 'flex' }}"
            >
        @if ($icon)
        </div>
        @endif
    </div>
</div>
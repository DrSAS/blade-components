@props([
    'id' => '',
    'type' => 'info',
    'title' => false,
    'dismissable' => false,
    'href' => false,
    'btnText' => __('global.read_more'),
    'icon' => false,
    'bold' => true,
    'animated' => true,
])

@php
    $styles = [
        'info' => ['blue', 'fa-circle-info'],
        'danger' => ['red', 'fa-circle-exclamation'],
        'success' => ['green', 'fa-circle-check'],
        'warning' => ['yellow', 'fa-triangle-exclamation'],
        'note' => ['gray', 'fa-flag'],
    ];

    $currentIcon = ($icon !== false) ? $icon : $styles[$type][1];

    // J'ai besoin de générer un ID unique par appel de ce component.
    // J'imagine qu'on peut faire un truc moins bourrin une fois
    // que ce code sera dans une classe...
    $id = 'toast_' . crc32(uniqid('', true));
@endphp

<div {{ $attributes->merge([
    'class' => ($animated ? 'show-down' : '') . ' flex items-center flex-col w-full max-w-xs p-4 text-gray-500 rounded-lg shadow dark:text-gray-400 dark:bg-gray-800 backdrop-blur-xl bg-white/90 rounded-lg p-5 ' . ($bold ? "border border-{$styles[$type][0]}-500" : ""),
    'role' => 'alert',
    'id' => $id
]) }}>
    <div class="flex w-full justify-between gap-2">
        <div class="flex flex-col gap-2">
            @if (!empty($title))
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $title }}</span>
            @endif
            <div class="flex gap-4">
                <div>
                    @if ($currentIcon !== '')
                        <i class="fa-solid mt-1 {{ $currentIcon }} w-5 h-5 text-{{ $styles[$type][0] }}-500"></i>
                    @endif
                    <span class="sr-only">{{ ucfirst($type) }}</span>
                </div>
                <div class="text-sm font-normal content-center">
                    <div>
                        {{ $slot }}
                    </div>
                    @if (!empty($href))
                        <div class="flex mt-2.5">
                            <a href="{{ $href }}" class="text-white bg-{{ $styles[$type][0] }}-700 hover:bg-{{ $styles[$type][0] }}-800 focus:ring-4 focus:outline-none focus:ring-{{ $styles[$type][0] }}-300 font-medium rounded-lg text-xs px-3 py-1.5 mr-2 text-center inline-flex items-center dark:bg-{{ $styles[$type][0] }}-600 dark:hover:bg-{{ $styles[$type][0] }}-500 dark:focus:ring-{{ $styles[$type][0] }}-800">
                                {{-- <i class="fa-solid fa-eye -ml-0.5 mr-2 h-4 w-4"></i> --}}
                                {{ $btnText }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex">
            @if ($dismissable)
                <button type="button" class="bg-white justify-center items-center flex-shrink-0 text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 hover:bg-gray-100 inline-flex  dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700 p-2 h-fit" data-dismiss-target="#{{ $id }}" aria-label="{{ __('global.close') }}">
                    <span class="sr-only">{{ __('global.close') }}</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            @endif
        </div>
    </div>
</div>
@php
    // J'ai besoin de générer un ID unique par appel de ce component.
    // J'imagine qu'on peut faire un truc moins bourrin une fois
    // que ce code sera dans une classe...
    $randomId = 'popover_' . crc32(uniqid('', true));
@endphp

@props([
    'uid' => $randomId,

    // TODO : J'aurais préféré pouvoir faire un système où je déclare mes classes par défaut et pouvoir les surcharger.
    // ------ Ici, je « pousse » des classes et si je dois surcharger, je met un ! sur les classes ce qui n'est pas fou.
    'class' => ''
])

<button data-popover-target="{{ $randomId }}" data-popover-placement="bottom-end" type="button" class="cursor-default">
    <i class="fa-solid fa-circle-question w-3 h-3 ml-1.5"></i>
    <span class="sr-only">{{ __("Information") }}</span>
</button>

<div data-popover id="{{ $randomId }}" role="tooltip" class="absolute z-10 invisible inline-block transition-opacity duration-300 rounded-lg shadow-sm opacity-0 bg-black text-white text-xs font-normal {{ $class }}">
    <div class="p-3">
        {{ $slot }}
    </div>
    <div></div>
</div>
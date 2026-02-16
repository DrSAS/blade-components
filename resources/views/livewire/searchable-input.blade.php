<div class="flex flex-wrap gap-2">

    <label for="si_{{ $name }}" class="text-sm font-semibold text-gray-700 dark:text-white">
        {{ $label }}
        {{ ($required) ? ' *' : '' }}
    </label>

    <div class="relative block w-full">
        @if ($iconClass)
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <i class="fa-solid {{ $iconClass }} w-5 h-5 text-nav-header"></i>
            <span class="sr-only">{{ __("Icône de recherche") }}</span>
        </div>
        @endif
        <input
            type="text"
            id="si_{{ $name }}"
            name="{{ $name }}[input]"
            wire:model.live.debounce.300ms="userInput"
            class="{{ $class ?? '' }} {{ $iconClass ? 'pl-10' : '' }} bg-gray-950 border border-gray-700 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 overflow-auto"
            placeholder="{{ $placeholder }}"
            x-init="$el.focus()"
        >
        <input type="hidden" name="{{ $name }}[value]" value="{{ $value }}" />

        @if(count($searchSuggestions))
        <div class="flex flex-wrap flex-col border border-gray-700 rounded-lg p-4 text-sm w-full absolute mt-1 bg-gray-950 z-10 text-gray-300 text-left">
            <span class="mb-2 font-bold block">{{ __("Suggestions :") }}</span>
            <ul class="flex flex-wrap">
                @foreach($searchSuggestions as $suggestion)
                    <li wire:click="selectSuggestion('{{ $suggestion[$valueFieldFromSelection] }}', '{{ $suggestion[$finalValueFromSelection] }}')" class="flex border-0 border-b-2 border-b-gray-800 hover:bg-gray-800 w-full cursor-pointer p-2 gap-2 flex-col sm:flex-row sm:items-center">
                        @foreach ($displayFieldsInSuggestions as $field)
                            @if (isset($field['method']))
                                {{ $suggestion->{$field['method']}() }}
                            @elseif ($field['type'] === 'tag')
                                <span class="sm:bg-gray-50 sm:text-gray-600 text-gray-400 py-1 text-xs rounded px-2">{{ $suggestion[$field['value']] }}</span>
                            @else
                                {{ $suggestion[$field['value']] }}
                            @endif
                        @endforeach
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    @if (!empty($description))
        <p class="text-xs font-normal text-gray-500 dark:text-gray-300">{{ $description }}</p>
    @endif
</div>
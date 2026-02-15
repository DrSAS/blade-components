<section class="fixed top-24 right-12 flex flex-col gap-4 z-50">
    @if (session('success'))
        <x-blade-components::toast type="success" dismissable>
            {{ session('success') }}
        </x-blade-components>
    @endif

    @if (session('warning'))
        <x-blade-components::toast type="warning" dismissable>
            {{ session('warning') }}
        </x-blade-components>
    @endif

    @if ($errors->any())
        <x-blade-components::toast 
            dismissable
            type="danger"
            title="Une erreur est survenue"
            class="w-full max-w-3xl m-auto"
        >
            @if ($errors->count() > 1)
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            @else
                {{ $errors->first() }}
            @endif
        </x-blade-components>
    @endif
</section>
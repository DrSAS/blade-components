<?php

namespace DrSAS\BladeComponents;

use DrSAS\BladeComponents\Livewire\SearchableInput;
use DrSAS\BladeComponents\View\Composers\DatatableComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class BladeComponentsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'blade-components');

        View::composer('blade-components::components.datatable', DatatableComposer::class);

        Livewire::component('blade-components::searchable-input', SearchableInput::class);
    }
}
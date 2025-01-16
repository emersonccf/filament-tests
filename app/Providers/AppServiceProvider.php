<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Para manter a sintaxe <x-input /> para os componentes Livewire
        Blade::component('livewire.form.input', 'input');
        Blade::component('livewire.form.password-input', 'password-input');
    }
}

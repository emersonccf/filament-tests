<?php

namespace App\Providers;

use App\Filament\Pages\Auth\EmailVerification;
use App\Services\QuoteService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

use App\Models\Marca;         // Importe o modelo Marca
use App\Observers\MarcaObserver; // Importe o Observer
use App\Models\Modelo; // Importe o modelo Modelo
use App\Observers\ModeloObserver; // Importe o Observer de Modelo

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(QuoteService::class, function ($app) {
            return new QuoteService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Confirmação de E-mail')
                ->view(
                    'emails.email-verify',
                    ['url' => $url, 'user' => $notifiable]
                );
        });

        // Para manter a sintaxe <x-input /> para os componentes Livewire
        Blade::component('livewire.form.input', 'input');
        Blade::component('livewire.form.password-input', 'password-input');
        Blade::component('livewire.flash-card.flash-message', 'flash-message');

        // Observers
        Marca::observe(MarcaObserver::class); // Registre o Observer Marca
        Modelo::observe(ModeloObserver::class); //  Registre o Observer Modelo
    }
}

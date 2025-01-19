<?php
// FilamentBrasil - Login Redirect - Como alterar a URL de redirecionamento?
// https://youtu.be/xlDbikN25nk?si=JIwJsy_2BIEAzOzL

namespace App\Http\Response;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class CustomLoginResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        return redirect()->route('home');
    }
}

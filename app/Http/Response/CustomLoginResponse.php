<?php
// FilamentBrasil - Login Redirect - Como alterar a URL de redirecionamento?
// https://youtu.be/xlDbikN25nk?si=JIwJsy_2BIEAzOzL

namespace App\Http\Response;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class CustomLoginResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $user = auth()->user();
        if ($user->is_admin) {
            return redirect()->route('home'); //filament.adm.pages.dashboard
        } else {
            return redirect()->route('dashboard');
        }
    }
}

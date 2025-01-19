<?php
// FilamentBrasil - Login Redirect - Como alterar a URL de redirecionamento?
// https://youtu.be/xlDbikN25nk?si=JIwJsy_2BIEAzOzL

namespace App\Http\Response;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;

class CustomLogoutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse
    {
        return redirect()->to(
            Filament::hasLogin() ? route('home') : Filament::getUrl()
        );
    }
}

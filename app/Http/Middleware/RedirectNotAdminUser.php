<?php
//https://youtu.be/jimA0o8B43w?si=Yk7Z1Qkf3fWeMQSo
//Filament: Gerenciar o acesso ao painel usando middleware

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectNotAdminUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Filament::auth()->user();
        if ($user && !$user->is_admin) {
            //Filament::auth()->logout();
            //$request->session()->invalidate();
            //$request->session()->regenerateToken();
            //return redirect()->route('filament.adm.auth.login');
            return redirect()->route('home');
        }
        return $next($request);
    }
}

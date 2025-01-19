<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectNotActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Filament::auth()->user();
        if ($user && !$user->is_active) {
            Filament::auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            //return redirect()->route('filament.adm.auth.login');
            return redirect()->route('home');
        }
        return $next($request);
    }
}

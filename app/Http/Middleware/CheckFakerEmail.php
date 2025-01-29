<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckFakerEmail
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->hasVerifiedEmail() && Str::endsWith(auth()->user()->email, '@faker.com')) {
            return redirect()->route('admin.activation.required');
        }

        return $next($request);
    }
}

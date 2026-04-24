<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureActiveUser
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && ! auth()->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Conta desactivada. Contacte o administrador.']);
        }

        return $next($request);
    }
}

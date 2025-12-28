<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalCompte
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('portal_compte_id')) {
            return redirect()->route('portal.compte.login')->with('error', 'Veuillez vous connecter pour accéder à votre espace.');
        }

        return $next($request);
    }
}

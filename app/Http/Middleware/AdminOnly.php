<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        // Liste des emails administrateurs autorisés
        $adminEmails = [
            'admin@dinor.com',
            // Ajoutez d'autres emails d'admin ici
        ];

        if (!auth()->check() || !in_array(auth()->user()->email, $adminEmails)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}
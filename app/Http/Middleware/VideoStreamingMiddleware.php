<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VideoStreamingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Ajouter les headers optimisés pour le streaming vidéo
        $response->headers->set('Accept-Ranges', 'bytes');
        $response->headers->set('Cache-Control', 'public, max-age=3600');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Headers pour éviter la compression sur les vidéos
        $response->headers->set('Content-Encoding', 'identity');
        $response->headers->set('Vary', 'Accept-Encoding');

        // Headers pour le streaming
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('Keep-Alive', 'timeout=5, max=1000');

        return $response;
    }
}

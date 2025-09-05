<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LivewireUploadMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Augmenter les limites pour les uploads Livewire
        if ($request->is('livewire/upload-file') || $request->is('livewire/*')) {
            // Augmenter la limite de mémoire pour les gros fichiers
            ini_set('memory_limit', '1024M');

            // Augmenter le temps d'exécution
            ini_set('max_execution_time', 300);

            // Augmenter la limite de taille de fichier
            ini_set('upload_max_filesize', '50M');
            ini_set('post_max_size', '50M');

            // Détecter si c'est un mobile
            $userAgent = $request->header('User-Agent', '');
            $isMobile = preg_match('/Mobile|Android|iPhone|iPad/', $userAgent);
            
            // Log pour debug avec détection mobile
            \Log::info('📱 LivewireUploadMiddleware applied', [
                'url' => $request->url(),
                'method' => $request->method(),
                'is_mobile' => $isMobile,
                'user_agent' => $userAgent,
                'content_length' => $request->header('Content-Length'),
                'content_type' => $request->header('Content-Type'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'memory_limit' => ini_get('memory_limit'),
                'has_files' => $request->hasFile('file') || $request->hasFile('photo'),
                'all_files' => array_keys($request->allFiles()),
            ]);
        }

        return $next($request);
    }
}

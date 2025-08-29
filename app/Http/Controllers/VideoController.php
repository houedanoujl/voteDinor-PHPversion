<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\HLSStreamingService;

class VideoController extends Controller
{
    /**
     * Stream une vidéo avec support des requêtes de plage
     */
    public function stream(Request $request, $filename = 'video.mp4')
    {
        $path = public_path('videos/' . $filename);

        // Vérifier si le fichier existe
        if (!File::exists($path)) {
            abort(404, 'Vidéo non trouvée');
        }

        $fileSize = File::size($path);
        $file = File::get($path);
        $mimeType = File::mimeType($path);

        // Vérifier si c'est une requête de plage (range request)
        $range = $request->header('Range');

        if ($range) {
            return $this->handleRangeRequest($path, $fileSize, $mimeType, $range);
        }

        // Retourner le fichier complet
        return response($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Gérer les requêtes de plage pour le streaming
     */
    private function handleRangeRequest($path, $fileSize, $mimeType, $range)
    {
        // Parser la requête de plage
        $range = str_replace('bytes=', '', $range);
        $ranges = explode('-', $range);
        $start = (int) $ranges[0];
        $end = isset($ranges[1]) && $ranges[1] !== '' ? (int) $ranges[1] : $fileSize - 1;

        // Calculer la longueur de la plage
        $length = $end - $start + 1;

        // Lire la partie demandée du fichier
        $file = File::get($path, false, $start, $length);

        return response($file, 206, [
            'Content-Type' => $mimeType,
            'Content-Length' => $length,
            'Content-Range' => "bytes {$start}-{$end}/{$fileSize}",
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Stream avec HLS (HTTP Live Streaming) - pour les vidéos longues
     */
    public function streamHLS(Request $request, $filename = 'video.mp4')
    {
        $hlsService = new HLSStreamingService(public_path('videos/' . $filename));

        // Vérifier si les streams HLS existent, sinon les générer
        if (!$hlsService->streamsExist()) {
            $hlsService->generateHLSStreams();
        }

        $masterPlaylistPath = public_path('videos/hls/master.m3u8');

        if (!File::exists($masterPlaylistPath)) {
            abort(404, 'Stream HLS non disponible');
        }

        $content = File::get($masterPlaylistPath);

        return response($content, 200, [
            'Content-Type' => 'application/vnd.apple.mpegurl',
            'Cache-Control' => 'no-cache',
        ]);
    }

    /**
     * Servir les segments HLS
     */
    public function serveHLSSegment(Request $request, $quality, $segment)
    {
        $segmentPath = public_path("videos/hls/{$quality}/{$segment}");

        if (!File::exists($segmentPath)) {
            abort(404, 'Segment non trouvé');
        }

        $content = File::get($segmentPath);
        $mimeType = 'video/MP2T';

        return response($content, 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Servir les playlists HLS
     */
    public function serveHLSPlaylist(Request $request, $quality)
    {
        $playlistPath = public_path("videos/hls/{$quality}/playlist.m3u8");

        if (!File::exists($playlistPath)) {
            abort(404, 'Playlist non trouvée');
        }

        $content = File::get($playlistPath);

        return response($content, 200, [
            'Content-Type' => 'application/vnd.apple.mpegurl',
            'Cache-Control' => 'no-cache',
        ]);
    }

    /**
     * Page de test pour le lecteur vidéo
     */
    public function player()
    {
        return view('video.player');
    }

    /**
     * API pour obtenir les informations de la vidéo
     */
    public function info(Request $request, $filename = 'video.mp4')
    {
        $path = public_path('videos/' . $filename);

        if (!File::exists($path)) {
            return response()->json(['error' => 'Vidéo non trouvée'], 404);
        }

        $fileSize = File::size($path);
        $mimeType = File::mimeType($path);
        $lastModified = File::lastModified($path);

        return response()->json([
            'filename' => $filename,
            'size' => $fileSize,
            'size_formatted' => $this->formatBytes($fileSize),
            'mime_type' => $mimeType,
            'last_modified' => date('Y-m-d H:i:s', $lastModified),
            'stream_url' => route('video.stream', $filename),
            'hls_url' => route('video.hls', $filename),
        ]);
    }

    /**
     * Formater les bytes en format lisible
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

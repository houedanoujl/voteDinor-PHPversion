<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VideoStreamingService
{
    /**
     * Stream une vidéo avec gestion optimisée des requêtes de plage
     */
    public function streamVideo(string $filename, Request $request): Response
    {
        $path = public_path('videos/' . $filename);

        if (!File::exists($path)) {
            abort(404, 'Vidéo non trouvée');
        }

        $fileSize = File::size($path);
        $mimeType = $this->getMimeType($filename);
        $range = $request->header('Range');

        if ($range) {
            return $this->handleRangeRequest($path, $fileSize, $mimeType, $range);
        }

        return $this->streamFullFile($path, $fileSize, $mimeType);
    }

    /**
     * Gérer les requêtes de plage pour le streaming
     */
    private function handleRangeRequest(string $path, int $fileSize, string $mimeType, string $range): Response
    {
        // Parser la requête de plage
        $range = str_replace('bytes=', '', $range);
        $ranges = explode('-', $range);
        $start = (int) $ranges[0];
        $end = isset($ranges[1]) && $ranges[1] !== '' ? (int) $ranges[1] : $fileSize - 1;

        // Validation des plages
        if ($start >= $fileSize || $end >= $fileSize || $start > $end) {
            return response('Requested Range Not Satisfiable', 416, [
                'Content-Range' => "bytes */{$fileSize}",
            ]);
        }

        $length = $end - $start + 1;

        return response()->stream(function () use ($path, $start, $length) {
            $handle = fopen($path, 'rb');
            fseek($handle, $start);

            $remaining = $length;
            $chunkSize = 8192; // 8KB chunks

            while ($remaining > 0 && !feof($handle)) {
                $readSize = min($chunkSize, $remaining);
                echo fread($handle, $readSize);
                $remaining -= $readSize;
                flush();
            }

            fclose($handle);
        }, 206, [
            'Content-Type' => $mimeType,
            'Content-Length' => $length,
            'Content-Range' => "bytes {$start}-{$end}/{$fileSize}",
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Stream le fichier complet
     */
    private function streamFullFile(string $path, int $fileSize, string $mimeType): Response
    {
        return response()->stream(function () use ($path) {
            $handle = fopen($path, 'rb');

            while (!feof($handle)) {
                echo fread($handle, 8192); // 8KB chunks
                flush();
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Créer un manifest HLS pour le streaming adaptatif
     */
    public function createHLSManifest(string $filename): Response
    {
        $path = public_path('videos/' . $filename);

        if (!File::exists($path)) {
            abort(404, 'Vidéo non trouvée');
        }

        // Simuler un manifest HLS simple
        $manifest = "#EXTM3U\n";
        $manifest .= "#EXT-X-VERSION:3\n";
        $manifest .= "#EXT-X-TARGETDURATION:10\n";
        $manifest .= "#EXT-X-MEDIA-SEQUENCE:0\n";
        $manifest .= "#EXTINF:10.0,\n";
        $manifest .= route('video.stream', $filename) . "\n";
        $manifest .= "#EXT-X-ENDLIST\n";

        return response($manifest, 200, [
            'Content-Type' => 'application/vnd.apple.mpegurl',
            'Cache-Control' => 'no-cache',
        ]);
    }

    /**
     * Obtenir le type MIME basé sur l'extension
     */
    private function getMimeType(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $mimeTypes = [
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'mkv' => 'video/x-matroska',
        ];

        return $mimeTypes[$extension] ?? 'video/mp4';
    }

    /**
     * Obtenir les informations de la vidéo
     */
    public function getVideoInfo(string $filename): array
    {
        $path = public_path('videos/' . $filename);

        if (!File::exists($path)) {
            throw new \Exception('Vidéo non trouvée');
        }

        $fileSize = File::size($path);
        $mimeType = $this->getMimeType($filename);
        $lastModified = File::lastModified($path);

        return [
            'filename' => $filename,
            'size' => $fileSize,
            'size_formatted' => $this->formatBytes($fileSize),
            'mime_type' => $mimeType,
            'last_modified' => date('Y-m-d H:i:s', $lastModified),
            'duration' => $this->getVideoDuration($path),
            'resolution' => $this->getVideoResolution($path),
            'bitrate' => $this->getVideoBitrate($path),
        ];
    }

    /**
     * Formater les bytes en format lisible
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Obtenir la durée de la vidéo (nécessite ffprobe)
     */
    private function getVideoDuration(string $path): ?string
    {
        if (!function_exists('exec')) {
            return null;
        }

        $command = "ffprobe -v quiet -show_entries format=duration -of csv=p=0 " . escapeshellarg($path);
        $duration = exec($command);

        return $duration ? gmdate('H:i:s', (int) $duration) : null;
    }

    /**
     * Obtenir la résolution de la vidéo (nécessite ffprobe)
     */
    private function getVideoResolution(string $path): ?string
    {
        if (!function_exists('exec')) {
            return null;
        }

        $command = "ffprobe -v quiet -select_streams v:0 -show_entries stream=width,height -of csv=p=0 " . escapeshellarg($path);
        $resolution = exec($command);

        return $resolution ?: null;
    }

    /**
     * Obtenir le bitrate de la vidéo (nécessite ffprobe)
     */
    private function getVideoBitrate(string $path): ?string
    {
        if (!function_exists('exec')) {
            return null;
        }

        $command = "ffprobe -v quiet -show_entries format=bit_rate -of csv=p=0 " . escapeshellarg($path);
        $bitrate = exec($command);

        if ($bitrate) {
            $bitrateKbps = round($bitrate / 1000);
            return $bitrateKbps . ' kbps';
        }

        return null;
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class HLSStreamingService
{
    private string $videoPath;
    private string $outputPath;
    private array $resolutions = [
        '1080p' => ['width' => 1920, 'height' => 1080, 'bitrate' => '5000k'],
        '720p' => ['width' => 1280, 'height' => 720, 'bitrate' => '2800k'],
        '480p' => ['width' => 854, 'height' => 480, 'bitrate' => '1400k'],
        '360p' => ['width' => 640, 'height' => 360, 'bitrate' => '800k'],
    ];

    public function __construct(string $videoPath = null)
    {
        $this->videoPath = $videoPath ?? public_path('videos/video.mp4');
        $this->outputPath = public_path('videos/hls');
    }

    /**
     * Génère les segments HLS pour toutes les résolutions
     */
    public function generateHLSStreams(): bool
    {
        try {
            // Créer le dossier de sortie
            if (!file_exists($this->outputPath)) {
                mkdir($this->outputPath, 0755, true);
            }

            // Vérifier que la vidéo source existe
            if (!file_exists($this->videoPath)) {
                throw new \Exception("Vidéo source non trouvée: {$this->videoPath}");
            }

            // Générer les streams pour chaque résolution
            $streams = [];
            foreach ($this->resolutions as $quality => $config) {
                $streamPath = $this->outputPath . "/{$quality}";
                if (!file_exists($streamPath)) {
                    mkdir($streamPath, 0755, true);
                }

                $this->generateStream($quality, $config);
                $streams[] = [
                    'quality' => $quality,
                    'path' => "/videos/hls/{$quality}/playlist.m3u8",
                    'resolution' => "{$config['width']}x{$config['height']}",
                    'bitrate' => $config['bitrate']
                ];
            }

            // Générer le manifest principal
            $this->generateMasterPlaylist($streams);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération HLS: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Génère un stream pour une résolution spécifique
     */
    private function generateStream(string $quality, array $config): void
    {
        $outputDir = $this->outputPath . "/{$quality}";
        $playlistPath = "{$outputDir}/playlist.m3u8";
        $segmentPath = "{$outputDir}/segment_%03d.ts";

        $ffmpegCommand = [
            'ffmpeg',
            '-i', $this->videoPath,
            '-c:v', 'libx264',
            '-c:a', 'aac',
            '-b:v', $config['bitrate'],
            '-maxrate', $config['bitrate'],
            '-bufsize', $config['bitrate'],
            '-vf', "scale={$config['width']}:{$config['height']}",
            '-hls_time', '10',
            '-hls_list_size', '0',
            '-hls_segment_filename', $segmentPath,
            '-f', 'hls',
            $playlistPath
        ];

        $command = implode(' ', $ffmpegCommand);
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception("Erreur FFmpeg pour {$quality}: " . implode(' ', $output));
        }
    }

    /**
     * Génère le manifest principal (master playlist)
     */
    private function generateMasterPlaylist(array $streams): void
    {
        $content = "#EXTM3U\n";
        $content .= "#EXT-X-VERSION:3\n\n";

        foreach ($streams as $stream) {
            $content .= "#EXT-X-STREAM-INF:BANDWIDTH=" . $this->getBandwidth($stream['bitrate']) . ",RESOLUTION={$stream['resolution']}\n";
            $content .= $stream['path'] . "\n";
        }

        file_put_contents($this->outputPath . '/master.m3u8', $content);
    }

    /**
     * Convertit le bitrate en bandwidth
     */
    private function getBandwidth(string $bitrate): int
    {
        return (int) str_replace('k', '000', $bitrate);
    }

    /**
     * Vérifie si les streams HLS existent
     */
    public function streamsExist(): bool
    {
        $masterPlaylist = $this->outputPath . '/master.m3u8';
        return file_exists($masterPlaylist);
    }

    /**
     * Obtient l'URL du master playlist
     */
    public function getMasterPlaylistUrl(): string
    {
        return url('/videos/hls/master.m3u8');
    }

    /**
     * Obtient les informations de la vidéo
     */
    public function getVideoInfo(): array
    {
        $ffprobeCommand = [
            'ffprobe',
            '-v', 'quiet',
            '-print_format', 'json',
            '-show_format',
            '-show_streams',
            $this->videoPath
        ];

        $command = implode(' ', $ffprobeCommand);
        $output = shell_exec($command);
        $data = json_decode($output, true);

        if (!$data) {
            return [
                'duration' => 0,
                'width' => 0,
                'height' => 0,
                'bitrate' => 0,
                'size' => filesize($this->videoPath)
            ];
        }

        $videoStream = collect($data['streams'])->firstWhere('codec_type', 'video');
        $format = $data['format'];

        return [
            'duration' => (float) $format['duration'],
            'width' => (int) $videoStream['width'],
            'height' => (int) $videoStream['height'],
            'bitrate' => (int) $format['bit_rate'],
            'size' => (int) $format['size']
        ];
    }

    /**
     * Nettoie les anciens segments
     */
    public function cleanupOldSegments(): void
    {
        $directories = glob($this->outputPath . '/*', GLOB_ONLYDIR);

        foreach ($directories as $dir) {
            $segments = glob($dir . '/segment_*.ts');
            $playlist = $dir . '/playlist.m3u8';

            // Garder seulement les 10 derniers segments
            if (count($segments) > 10) {
                $segmentsToDelete = array_slice($segments, 0, count($segments) - 10);
                foreach ($segmentsToDelete as $segment) {
                    unlink($segment);
                }
            }
        }
    }
}

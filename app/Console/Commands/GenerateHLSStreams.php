<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HLSStreamingService;

class GenerateHLSStreams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:generate-hls {video? : Nom du fichier vidéo (défaut: video.mp4)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Génère les streams HLS adaptatifs pour une vidéo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $videoFile = $this->argument('video') ?? 'video.mp4';
        $videoPath = public_path("videos/{$videoFile}");

        if (!file_exists($videoPath)) {
            $this->error("Vidéo non trouvée: {$videoPath}");
            return 1;
        }

        $this->info("Génération des streams HLS pour: {$videoFile}");
        $this->info("Résolutions: 1080p, 720p, 480p, 360p");

        $hlsService = new HLSStreamingService($videoPath);

        $this->info("Début de la génération...");
        $progressBar = $this->output->createProgressBar(4);
        $progressBar->start();

        try {
            $success = $hlsService->generateHLSStreams();

            if ($success) {
                $progressBar->finish();
                $this->newLine();
                $this->info("✅ Streams HLS générés avec succès !");

                // Afficher les informations de la vidéo
                $videoInfo = $hlsService->getVideoInfo();
                $this->info("📊 Informations de la vidéo:");
                $this->table(
                    ['Propriété', 'Valeur'],
                    [
                        ['Durée', gmdate('H:i:s', (int)$videoInfo['duration'])],
                        ['Résolution', "{$videoInfo['width']}x{$videoInfo['height']}"],
                        ['Taille', $this->formatBytes($videoInfo['size'])],
                        ['Bitrate', $this->formatBytes($videoInfo['bitrate']) . '/s'],
                    ]
                );

                $this->info("🎥 Master playlist disponible: " . $hlsService->getMasterPlaylistUrl());
                $this->info("📁 Fichiers générés dans: public/videos/hls/");
            } else {
                $this->error("❌ Erreur lors de la génération des streams HLS");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Erreur: " . $e->getMessage());
            return 1;
        }

        return 0;
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

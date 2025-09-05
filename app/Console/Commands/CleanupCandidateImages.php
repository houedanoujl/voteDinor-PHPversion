<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use App\Services\ImageOptimizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CleanupCandidateImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'candidates:cleanup-images {--regenerate : Regenerate thumbnails for all candidates}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up candidate images and fix double _thumb suffixes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting candidate images cleanup...');

        $regenerate = $this->option('regenerate');

        if ($regenerate) {
            $this->regenerateAllThumbnails();
        } else {
            $this->cleanupDoubleThumbFiles();
        }

        $this->info('Cleanup completed successfully!');
    }

    /**
     * Clean up files with double _thumb suffixes
     */
    private function cleanupDoubleThumbFiles()
    {
        $this->info('Cleaning up double _thumb files...');

        $candidatesDir = storage_path('app/public/candidates');
        $files = glob($candidatesDir . '/*_thumb_thumb.*');

        $cleaned = 0;
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
                $cleaned++;
                $this->line("Removed: " . basename($file));
            }
        }

        $this->info("Cleaned up {$cleaned} double _thumb files.");
    }

    /**
     * Regenerate thumbnails for all candidates
     */
    private function regenerateAllThumbnails()
    {
        $this->info('Regenerating thumbnails for all candidates...');

        $imageService = app(ImageOptimizationService::class);
        $candidates = Candidate::whereNotNull('photo_url')->get();

        $processed = 0;
        $errors = 0;

        foreach ($candidates as $candidate) {
            try {
                $photoUrl = $candidate->getPhotoUrl();

                if ($photoUrl && $photoUrl !== '/images/placeholder-avatar.svg') {
                    // Extraire le chemin relatif
                    $relativePath = str_replace(Storage::disk('public')->url(''), '', $photoUrl);
                    $fullPath = Storage::disk('public')->path($relativePath);

                    if (file_exists($fullPath)) {
                        // Nettoyer les anciennes versions
                        $imageService->cleanupOldVersions($relativePath, 'candidates');

                        // Régénérer les versions optimisées
                        $imageService->optimizeImage($fullPath, 'candidates');

                        $processed++;
                        $this->line("Processed: {$candidate->prenom} {$candidate->nom}");
                    } else {
                        $this->warn("File not found: {$fullPath}");
                        $errors++;
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error processing {$candidate->prenom} {$candidate->nom}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->info("Processed {$processed} candidates with {$errors} errors.");
    }
}

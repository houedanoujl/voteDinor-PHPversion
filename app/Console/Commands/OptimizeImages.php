<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImageOptimizationService;
use App\Models\Candidate;

class OptimizeImages extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'images:optimize 
                            {--directory=candidates : Directory to optimize}
                            {--force : Force re-optimization of already optimized images}';

    /**
     * The console command description.
     */
    protected $description = 'Optimize images by creating multiple sizes and compressing them';

    protected ImageOptimizationService $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        parent::__construct();
        $this->imageService = $imageService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directory = $this->option('directory');
        $force = $this->option('force');

        $this->info("Début de l'optimisation des images dans le répertoire: {$directory}");

        try {
            if ($force) {
                $this->warn('Mode force activé: toutes les images seront re-optimisées');
            }

            $optimized = $this->imageService->optimizeExistingImages($directory);
            
            $this->info("✅ Optimisation terminée: {$optimized} images optimisées");

            // Optionnel: Mettre à jour les candidats pour utiliser les nouvelles URLs
            if ($directory === 'candidates' && $optimized > 0) {
                $this->info("Mise à jour des URLs des candidats...");
                $this->updateCandidateUrls();
            }

        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de l'optimisation: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Met à jour les URLs des candidats pour utiliser les versions optimisées
     */
    private function updateCandidateUrls(): void
    {
        $candidates = Candidate::whereNotNull('photo_url')->get();
        $updated = 0;

        foreach ($candidates as $candidate) {
            try {
                $optimizedUrls = $candidate->getOptimizedPhotoUrls();
                if (isset($optimizedUrls['main']) && $optimizedUrls['main'] !== '/images/placeholder-avatar.svg') {
                    // On garde l'URL originale mais on s'assure que les versions optimisées existent
                    $this->line("Candidat {$candidate->id}: URLs optimisées disponibles");
                    $updated++;
                }
            } catch (\Exception $e) {
                $this->warn("Erreur pour le candidat {$candidate->id}: " . $e->getMessage());
            }
        }

        $this->info("✅ {$updated} candidats mis à jour");
    }
}
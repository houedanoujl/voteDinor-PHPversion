<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use App\Events\CandidatePhotoUploaded;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeExistingCandidateImages extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'candidates:optimize-images 
                            {--force : Force re-optimization of already optimized images}
                            {--limit=10 : Limit the number of candidates to process}';

    /**
     * The console command description.
     */
    protected $description = 'Optimize existing candidate images by dispatching optimization events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        $limit = (int) $this->option('limit');

        $this->info("Début de l'optimisation des images candidats existantes...");
        
        // Construire la query
        $query = Candidate::whereNotNull('photo_url')
                          ->where('photo_url', '!=', '');

        if (!$force) {
            $query->whereIn('photo_optimization_status', ['pending', 'failed'])
                  ->orWhereNull('photo_optimization_status');
        }

        $candidates = $query->limit($limit)->get();

        if ($candidates->isEmpty()) {
            $this->info('Aucun candidat trouvé pour optimisation.');
            return Command::SUCCESS;
        }

        $this->info("Candidats trouvés : {$candidates->count()}");

        $optimized = 0;
        $errors = 0;

        foreach ($candidates as $candidate) {
            try {
                // Vérifier que le fichier existe
                if (!Storage::disk('public')->exists($candidate->photo_url)) {
                    $this->warn("Fichier non trouvé pour le candidat {$candidate->id}: {$candidate->photo_url}");
                    continue;
                }

                // Marquer comme en traitement
                $candidate->update(['photo_optimization_status' => 'processing']);

                // Déclencher l'événement d'optimisation
                CandidatePhotoUploaded::dispatch($candidate, $candidate->photo_url);

                $this->line("✅ Événement d'optimisation déclenché pour: {$candidate->prenom} {$candidate->nom} (ID: {$candidate->id})");
                $optimized++;

            } catch (\Exception $e) {
                $this->error("❌ Erreur pour le candidat {$candidate->id}: " . $e->getMessage());
                $errors++;
                
                $candidate->update([
                    'photo_optimization_status' => 'failed',
                    'photo_optimization_error' => $e->getMessage()
                ]);
            }
        }

        $this->newLine();
        $this->info("📊 Résumé:");
        $this->info("   • Événements d'optimisation déclenchés: {$optimized}");
        $this->info("   • Erreurs: {$errors}");
        
        if ($optimized > 0) {
            $this->newLine();
            $this->comment("💡 Les optimisations sont en cours de traitement en arrière-plan.");
            $this->comment("   Surveillez les logs ou utilisez 'php artisan queue:work' si les queues sont configurées.");
        }

        return Command::SUCCESS;
    }
}
<?php

namespace App\Listeners;

use App\Events\CandidatePhotoUploaded;
use App\Services\ImageOptimizationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OptimizeCandidatePhoto // Supprimer ShouldQueue pour exécution synchrone temporaire
{
    use InteractsWithQueue;

    protected ImageOptimizationService $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function handle(CandidatePhotoUploaded $event): void
    {
        try {
            $candidate = $event->candidate;
            $photoPath = $event->photoPath;
            
            // Déterminer si c'est un fichier HEIC
            $isHeic = strtolower(pathinfo($photoPath, PATHINFO_EXTENSION)) === 'heic';
            
            Log::info('Optimisation photo candidat - Début', [
                'candidate_id' => $candidate->id,
                'candidate_name' => $candidate->prenom . ' ' . $candidate->nom,
                'photo_path' => $photoPath,
                'is_heic' => $isHeic
            ]);

            // Vérifier que le fichier existe
            if (!Storage::disk('public')->exists($photoPath)) {
                Log::error('Fichier photo non trouvé pour optimisation', [
                    'candidate_id' => $candidate->id,
                    'photo_path' => $photoPath
                ]);
                return;
            }

            $fullPath = Storage::disk('public')->path($photoPath);
            
            // Optimiser l'image (gère automatiquement HEIC avec préservation)
            $optimizedImages = $this->imageService->optimizeImage($fullPath, 'candidates');

            // Si c'était un HEIC et qu'on a créé un JPEG converti, mettre à jour le candidat
            if ($isHeic) {
                $jpegFilename = pathinfo($photoPath, PATHINFO_FILENAME) . '_converted.jpg';
                $jpegPath = 'candidates/' . $jpegFilename;
                
                if (Storage::disk('public')->exists($jpegPath)) {
                    $candidate->update([
                        'photo_url' => Storage::disk('public')->url($jpegPath),
                        'photo_filename' => $jpegFilename,
                    ]);
                    
                    Log::info('Photo HEIC convertie et URL mise à jour', [
                        'candidate_id' => $candidate->id,
                        'original_heic' => $photoPath,
                        'converted_jpeg' => $jpegPath
                    ]);
                }
            }

            Log::info('Optimisation photo candidat - Succès', [
                'candidate_id' => $candidate->id,
                'candidate_name' => $candidate->prenom . ' ' . $candidate->nom,
                'original_path' => $photoPath,
                'optimized_versions' => array_keys($optimizedImages),
                'sizes_created' => count($optimizedImages),
                'is_heic_converted' => $isHeic
            ]);

            // Mettre à jour le candidat avec des métadonnées d'optimisation
            $candidate->update([
                'photo_optimized_at' => now(),
                'photo_optimization_status' => 'completed'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'optimisation photo candidat', [
                'candidate_id' => $event->candidate->id,
                'photo_path' => $event->photoPath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Marquer comme échoué
            $event->candidate->update([
                'photo_optimization_status' => 'failed',
                'photo_optimization_error' => $e->getMessage()
            ]);
        }
    }

    public function failed(CandidatePhotoUploaded $event, \Throwable $exception): void
    {
        Log::error('Échec définitif de l\'optimisation photo candidat', [
            'candidate_id' => $event->candidate->id,
            'photo_path' => $event->photoPath,
            'error' => $exception->getMessage()
        ]);

        $event->candidate->update([
            'photo_optimization_status' => 'failed',
            'photo_optimization_error' => $exception->getMessage()
        ]);
    }
}
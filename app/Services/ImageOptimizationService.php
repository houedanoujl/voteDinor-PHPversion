<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageOptimizationService
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Optimise une image en créant plusieurs tailles et en compressant
     * 
     * @param string $imagePath Chemin vers l'image originale
     * @param string $directory Répertoire de destination
     * @return array Tableau avec les chemins des images optimisées
     */
    public function optimizeImage(string $imagePath, string $directory = 'candidates'): array
    {
        try {
            $image = $this->manager->read($imagePath);
            $filename = pathinfo($imagePath, PATHINFO_FILENAME);
            $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
            
            $optimizedImages = [];

            // Image principale (hauteur max 600px, qualité 85)
            $mainImage = clone $image;
            $mainImage->resize(null, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
            $mainPath = "{$directory}/{$filename}_main.{$extension}";
            Storage::disk('public')->put($mainPath, $mainImage->toJpeg(85));
            $optimizedImages['main'] = $mainPath;

            // Thumbnail (300x300 croppé, qualité 80)
            $thumbImage = clone $image;
            $thumbImage->cover(300, 300);
            
            $thumbPath = "{$directory}/{$filename}_thumb.{$extension}";
            Storage::disk('public')->put($thumbPath, $thumbImage->toJpeg(80));
            $optimizedImages['thumb'] = $thumbPath;

            // Small thumbnail pour les listes (150x150, qualité 75)
            $smallThumbImage = clone $image;
            $smallThumbImage->resize(150, 150, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
            $smallThumbPath = "{$directory}/{$filename}_small.{$extension}";
            Storage::disk('public')->put($smallThumbPath, $smallThumbImage->toJpeg(75));
            $optimizedImages['small'] = $smallThumbPath;

            // WebP versions pour les navigateurs compatibles
            if (function_exists('imagewebp')) {
                // WebP principal
                $webpMainPath = "{$directory}/{$filename}_main.webp";
                Storage::disk('public')->put($webpMainPath, $mainImage->toWebp(85));
                $optimizedImages['main_webp'] = $webpMainPath;

                // WebP thumbnail
                $webpThumbPath = "{$directory}/{$filename}_thumb.webp";
                Storage::disk('public')->put($webpThumbPath, $thumbImage->toWebp(80));
                $optimizedImages['thumb_webp'] = $webpThumbPath;
            }

            Log::info("Images optimisées avec succès", [
                'original' => $imagePath,
                'optimized' => array_keys($optimizedImages)
            ]);

            return $optimizedImages;

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'optimisation d'image: " . $e->getMessage(), [
                'image_path' => $imagePath
            ]);
            
            throw $e;
        }
    }

    /**
     * Optimise toutes les images existantes d'un répertoire
     * 
     * @param string $directory
     * @return int Nombre d'images optimisées
     */
    public function optimizeExistingImages(string $directory = 'candidates'): int
    {
        $files = Storage::disk('public')->files($directory);
        $optimized = 0;

        foreach ($files as $file) {
            // Éviter de re-optimiser les images déjà optimisées
            if (strpos($file, '_main') !== false || 
                strpos($file, '_thumb') !== false || 
                strpos($file, '_small') !== false) {
                continue;
            }

            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                try {
                    $fullPath = Storage::disk('public')->path($file);
                    $this->optimizeImage($fullPath, $directory);
                    $optimized++;
                } catch (\Exception $e) {
                    Log::warning("Impossible d'optimiser l'image: {$file}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $optimized;
    }

    /**
     * Supprime les anciennes versions d'une image optimisée
     * 
     * @param string $filename
     * @param string $directory
     */
    public function cleanupOldVersions(string $filename, string $directory = 'candidates'): void
    {
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $patterns = [
            "{$directory}/{$baseName}_main.*",
            "{$directory}/{$baseName}_thumb.*",
            "{$directory}/{$baseName}_small.*",
        ];

        foreach ($patterns as $pattern) {
            $files = glob(Storage::disk('public')->path($pattern));
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Retourne les URLs optimisées pour une image
     * 
     * @param string $originalPath
     * @return array
     */
    public function getOptimizedUrls(string $originalPath): array
    {
        $pathInfo = pathinfo($originalPath);
        $directory = dirname($originalPath);
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'];

        $urls = [
            'original' => Storage::disk('public')->url($originalPath),
            'main' => Storage::disk('public')->url("{$directory}/{$filename}_main.{$extension}"),
            'thumb' => Storage::disk('public')->url("{$directory}/{$filename}_thumb.{$extension}"),
            'small' => Storage::disk('public')->url("{$directory}/{$filename}_small.{$extension}"),
        ];

        // Ajouter les versions WebP si elles existent
        $webpMain = "{$directory}/{$filename}_main.webp";
        $webpThumb = "{$directory}/{$filename}_thumb.webp";
        
        if (Storage::disk('public')->exists($webpMain)) {
            $urls['main_webp'] = Storage::disk('public')->url($webpMain);
        }
        
        if (Storage::disk('public')->exists($webpThumb)) {
            $urls['thumb_webp'] = Storage::disk('public')->url($webpThumb);
        }

        return $urls;
    }
}
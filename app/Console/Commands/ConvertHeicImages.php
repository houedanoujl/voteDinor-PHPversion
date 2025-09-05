<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Candidate;

class ConvertHeicImages extends Command
{
    protected $signature = 'candidates:convert-heic {--dir=candidates : Dossier à scanner} {--backup : Conserver les fichiers originaux} {--update-db : Mettre à jour les URLs en base}';

    protected $description = '🍎 Convertit tous les fichiers HEIC en JPEG (Anti-iOS)';

    private array $stats = ['converted' => 0, 'errors' => 0, 'updated' => 0];

    public function handle()
    {
        $this->info('🍎 CONVERSION HEIC → JPEG (Anti-iOS)');
        $this->line(str_repeat('=', 60));

        $directory = $this->option('dir');
        $backup = $this->option('backup');
        $updateDb = $this->option('update-db');

        $this->info("📁 Dossier: storage/app/public/{$directory}");
        $this->line('');

        // Créer le dossier de backup si nécessaire
        if ($backup) {
            $backupDir = $directory . '/heic_backup';
            if (!Storage::disk('public')->exists($backupDir)) {
                Storage::disk('public')->makeDirectory($backupDir);
                $this->info("📦 Dossier de sauvegarde créé: {$backupDir}");
            }
        }

        // Trouver les fichiers HEIC
        $heicFiles = $this->findHeicFiles($directory);

        if (empty($heicFiles)) {
            $this->info('✅ Aucun fichier HEIC trouvé.');
            return Command::SUCCESS;
        }

        $this->info("🔍 Trouvé " . count($heicFiles) . " fichier(s) HEIC");
        $this->line('');

        $bar = $this->output->createProgressBar(count($heicFiles));
        $bar->start();

        foreach ($heicFiles as $heicFile) {
            $this->convertFile($heicFile, $directory, $backup, $updateDb);
            $bar->advance();
        }

        $bar->finish();
        $this->line('');
        $this->line('');

        $this->showResults();
        
        return Command::SUCCESS;
    }

    private function findHeicFiles(string $directory): array
    {
        $extensions = ['heic', 'HEIC', 'heif', 'HEIF'];
        $files = [];

        foreach ($extensions as $ext) {
            $pattern = $directory . "/*.{$ext}";
            $found = Storage::disk('public')->files($directory);
            
            foreach ($found as $file) {
                $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($fileExt, ['heic', 'heif'])) {
                    $files[] = $file;
                }
            }
        }

        return $files;
    }

    private function convertFile(string $heicFile, string $directory, bool $backup, bool $updateDb): void
    {
        try {
            $pathInfo = pathinfo($heicFile);
            $filename = $pathInfo['filename'];
            $jpegFile = $pathInfo['dirname'] . '/' . $filename . '.jpg';

            $originalPath = Storage::disk('public')->path($heicFile);
            $jpegPath = Storage::disk('public')->path($jpegFile);

            // Méthode 1: ImageMagick
            if ($this->convertWithImageMagick($originalPath, $jpegPath)) {
                $this->handleSuccessfulConversion($heicFile, $jpegFile, $backup, $updateDb);
                return;
            }

            // Méthode 2: FFmpeg
            if ($this->convertWithFFmpeg($originalPath, $jpegPath)) {
                $this->handleSuccessfulConversion($heicFile, $jpegFile, $backup, $updateDb);
                return;
            }

            // Méthode 3: Intervention Image
            if ($this->convertWithIntervention($originalPath, $jpegPath)) {
                $this->handleSuccessfulConversion($heicFile, $jpegFile, $backup, $updateDb);
                return;
            }

            $this->stats['errors']++;
            Log::error("Impossible de convertir: {$heicFile}");

        } catch (\Exception $e) {
            $this->stats['errors']++;
            Log::error("Erreur conversion HEIC: " . $e->getMessage(), ['file' => $heicFile]);
        }
    }

    private function convertWithImageMagick(string $input, string $output): bool
    {
        exec('which convert', $out, $return);
        if ($return !== 0) return false;

        $command = sprintf(
            'convert %s -quality 85 -strip -auto-orient %s 2>/dev/null',
            escapeshellarg($input),
            escapeshellarg($output)
        );

        exec($command, $out, $return);
        return $return === 0 && file_exists($output) && filesize($output) > 0;
    }

    private function convertWithFFmpeg(string $input, string $output): bool
    {
        exec('which ffmpeg', $out, $return);
        if ($return !== 0) return false;

        $command = sprintf(
            'ffmpeg -i %s -q:v 2 %s -y 2>/dev/null',
            escapeshellarg($input),
            escapeshellarg($output)
        );

        exec($command, $out, $return);
        return $return === 0 && file_exists($output) && filesize($output) > 0;
    }

    private function convertWithIntervention(string $input, string $output): bool
    {
        try {
            $manager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );

            $image = $manager->read($input);
            $image->toJpeg(85)->save($output);
            
            return file_exists($output) && filesize($output) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function handleSuccessfulConversion(string $heicFile, string $jpegFile, bool $backup, bool $updateDb): void
    {
        // Backup si demandé
        if ($backup) {
            $backupPath = dirname($heicFile) . '/heic_backup/' . basename($heicFile);
            Storage::disk('public')->copy($heicFile, $backupPath);
        }

        // Supprimer l'original
        Storage::disk('public')->delete($heicFile);

        // Mettre à jour la base de données
        if ($updateDb) {
            $this->updateCandidatePhoto($heicFile, $jpegFile);
        }

        $this->stats['converted']++;
    }

    private function updateCandidatePhoto(string $oldFile, string $newFile): void
    {
        $oldFilename = basename($oldFile);
        $newFilename = basename($newFile);

        $candidates = Candidate::where('photo_filename', $oldFilename)
                               ->orWhere('photo_url', 'like', "%{$oldFilename}")
                               ->get();

        foreach ($candidates as $candidate) {
            $candidate->update([
                'photo_filename' => $newFilename,
                'photo_url' => str_replace($oldFilename, $newFilename, $candidate->photo_url)
            ]);
            
            $this->stats['updated']++;
        }
    }

    private function showResults(): void
    {
        $this->info('📊 RÉSULTATS:');
        $this->table(
            ['Statut', 'Nombre'],
            [
                ['✅ Convertis', $this->stats['converted']],
                ['❌ Erreurs', $this->stats['errors']],
                ['🔄 BDD mise à jour', $this->stats['updated']],
            ]
        );

        if ($this->stats['converted'] > 0) {
            $this->info('');
            $this->info('🎉 Conversion terminée ! Fini les HEIC d\'iOS !');
        }
    }
}
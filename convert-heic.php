<?php
/**
 * Script de conversion HEIC vers JPEG
 * Usage: php convert-heic.php [dossier]
 * Si aucun dossier n'est spécifié, utilise storage/app/public/candidates
 */

require_once __DIR__ . '/vendor/autoload.php';

class HeicConverter
{
    private string $sourceDir;
    private string $backupDir;
    private array $stats = ['converted' => 0, 'errors' => 0, 'skipped' => 0];

    public function __construct(string $sourceDir)
    {
        $this->sourceDir = rtrim($sourceDir, '/');
        $this->backupDir = $this->sourceDir . '/heic_backup';
        
        if (!is_dir($this->sourceDir)) {
            throw new Exception("Le dossier source n'existe pas: {$this->sourceDir}");
        }
        
        // Créer le dossier de sauvegarde
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    public function convertAllHeicFiles(): array
    {
        echo "🔍 Recherche des fichiers HEIC dans: {$this->sourceDir}\n\n";
        
        $heicFiles = $this->findHeicFiles();
        
        if (empty($heicFiles)) {
            echo "✅ Aucun fichier HEIC trouvé.\n";
            return $this->stats;
        }
        
        echo "📁 Trouvé " . count($heicFiles) . " fichier(s) HEIC\n\n";
        
        foreach ($heicFiles as $heicFile) {
            $this->convertSingleFile($heicFile);
        }
        
        $this->showStats();
        return $this->stats;
    }

    private function findHeicFiles(): array
    {
        $heicFiles = [];
        $extensions = ['heic', 'HEIC', 'heif', 'HEIF'];
        
        foreach ($extensions as $ext) {
            $files = glob($this->sourceDir . "/*.{$ext}");
            $heicFiles = array_merge($heicFiles, $files);
        }
        
        return $heicFiles;
    }

    private function convertSingleFile(string $heicPath): bool
    {
        $filename = pathinfo($heicPath, PATHINFO_FILENAME);
        $jpegPath = $this->sourceDir . '/' . $filename . '.jpg';
        $backupPath = $this->backupDir . '/' . basename($heicPath);
        
        echo "🔄 Conversion: " . basename($heicPath) . " → " . basename($jpegPath) . "\n";
        
        try {
            // Méthode 1: ImageMagick (plus fiable pour HEIC)
            if ($this->convertWithImageMagick($heicPath, $jpegPath)) {
                $this->handleSuccessfulConversion($heicPath, $backupPath);
                return true;
            }
            
            // Méthode 2: FFmpeg (alternative)
            if ($this->convertWithFFmpeg($heicPath, $jpegPath)) {
                $this->handleSuccessfulConversion($heicPath, $backupPath);
                return true;
            }
            
            // Méthode 3: Intervention Image (dernier recours)
            if ($this->convertWithIntervention($heicPath, $jpegPath)) {
                $this->handleSuccessfulConversion($heicPath, $backupPath);
                return true;
            }
            
            echo "❌ Échec: Aucune méthode de conversion n'a fonctionné\n";
            $this->stats['errors']++;
            return false;
            
        } catch (Exception $e) {
            echo "❌ Erreur: " . $e->getMessage() . "\n";
            $this->stats['errors']++;
            return false;
        }
    }

    private function convertWithImageMagick(string $input, string $output): bool
    {
        // Vérifier si ImageMagick est disponible
        exec('which convert', $out, $return);
        if ($return !== 0) {
            return false;
        }
        
        $command = sprintf(
            'convert %s -quality 85 -strip %s 2>/dev/null',
            escapeshellarg($input),
            escapeshellarg($output)
        );
        
        exec($command, $out, $return);
        
        return $return === 0 && file_exists($output) && filesize($output) > 0;
    }

    private function convertWithFFmpeg(string $input, string $output): bool
    {
        // Vérifier si FFmpeg est disponible
        exec('which ffmpeg', $out, $return);
        if ($return !== 0) {
            return false;
        }
        
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
            if (!class_exists('Intervention\Image\ImageManager')) {
                return false;
            }
            
            $manager = new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            
            $image = $manager->read($input);
            $image->toJpeg(85)->save($output);
            
            return file_exists($output) && filesize($output) > 0;
            
        } catch (Exception $e) {
            return false;
        }
    }

    private function handleSuccessfulConversion(string $original, string $backup): void
    {
        // Sauvegarder l'original
        copy($original, $backup);
        
        // Supprimer l'original
        unlink($original);
        
        echo "✅ Converti avec succès (original sauvegardé)\n";
        $this->stats['converted']++;
    }

    private function showStats(): void
    {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "📊 RÉSULTATS DE LA CONVERSION\n";
        echo str_repeat("=", 50) . "\n";
        echo "✅ Fichiers convertis: {$this->stats['converted']}\n";
        echo "❌ Erreurs: {$this->stats['errors']}\n";
        echo "⏭️  Ignorés: {$this->stats['skipped']}\n";
        echo "\n💾 Fichiers originaux sauvegardés dans: {$this->backupDir}\n";
    }
}

// Script principal
try {
    $sourceDir = $argv[1] ?? __DIR__ . '/storage/app/public/candidates';
    
    echo "🍎 CONVERTISSEUR HEIC → JPEG (Anti-iOS)\n";
    echo str_repeat("=", 50) . "\n\n";
    
    $converter = new HeicConverter($sourceDir);
    $converter->convertAllHeicFiles();
    
} catch (Exception $e) {
    echo "💥 Erreur fatale: " . $e->getMessage() . "\n";
    exit(1);
}
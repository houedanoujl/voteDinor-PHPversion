<?php
/**
 * Script de debug pour les uploads mobiles
 * Usage: Accès via http://192.168.1.21:8080/debug-mobile-upload.php
 */

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Debug Upload Mobile - DINOR</title>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
echo "<style>";
echo "body { font-family: Arial; margin: 20px; background: #f5f5f5; }";
echo ".debug { background: white; padding: 20px; border-radius: 8px; margin: 10px 0; }";
echo ".success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; }";
echo ".error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; }";
echo ".info { background: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 4px; }";
echo "input[type=file] { width: 100%; padding: 10px; margin: 10px 0; }";
echo "button { background: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 4px; }";
echo "</style>";
echo "</head><body>";

echo "<h1>🔧 Debug Upload Mobile - DINOR</h1>";

// Informations système
echo "<div class='debug'>";
echo "<h2>📊 Informations Système</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Upload Max Filesize:</strong> " . ini_get('upload_max_filesize') . "</p>";
echo "<p><strong>Post Max Size:</strong> " . ini_get('post_max_size') . "</p>";
echo "<p><strong>Max File Uploads:</strong> " . ini_get('max_file_uploads') . "</p>";
echo "<p><strong>Memory Limit:</strong> " . ini_get('memory_limit') . "</p>";
echo "<p><strong>User Agent:</strong> " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Non défini') . "</p>";
echo "<p><strong>Is Mobile:</strong> " . (preg_match('/Mobile|Android|iPhone|iPad/', $_SERVER['HTTP_USER_AGENT'] ?? '') ? 'OUI' : 'NON') . "</p>";
echo "</div>";

// Test d'upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_photo'])) {
    echo "<div class='debug'>";
    echo "<h2>📤 Résultat du Test Upload</h2>";
    
    $file = $_FILES['test_photo'];
    
    echo "<div class='info'>";
    echo "<h3>Détails du fichier:</h3>";
    echo "<p><strong>Nom:</strong> " . htmlspecialchars($file['name']) . "</p>";
    echo "<p><strong>Type MIME:</strong> " . htmlspecialchars($file['type']) . "</p>";
    echo "<p><strong>Taille:</strong> " . $file['size'] . " bytes (" . round($file['size']/1024/1024, 2) . " MB)</p>";
    echo "<p><strong>Fichier temporaire:</strong> " . $file['tmp_name'] . "</p>";
    echo "<p><strong>Erreur:</strong> " . $file['error'] . "</p>";
    echo "</div>";
    
    // Analyse des erreurs
    if ($file['error'] === UPLOAD_ERR_OK) {
        echo "<div class='success'>";
        echo "<h3>✅ Upload réussi !</h3>";
        
        // Vérifier si c'est une image valide
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo) {
            echo "<p><strong>Type d'image:</strong> " . $imageInfo['mime'] . "</p>";
            echo "<p><strong>Dimensions:</strong> " . $imageInfo[0] . "x" . $imageInfo[1] . " pixels</p>";
        } else {
            echo "<div class='error'>❌ Le fichier n'est pas une image valide</div>";
        }
        
        // Test de sauvegarde
        $uploadDir = __DIR__ . '/storage/app/public/test_uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = 'mobile_test_' . date('Y-m-d_H-i-s') . '_' . $file['name'];
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            echo "<p><strong>✅ Fichier sauvegardé:</strong> " . $filename . "</p>";
            echo "<p><strong>Taille sur disque:</strong> " . filesize($targetPath) . " bytes</p>";
            
            // Nettoyer le fichier de test
            unlink($targetPath);
            echo "<p><em>Fichier de test supprimé</em></p>";
        } else {
            echo "<div class='error'>❌ Erreur lors de la sauvegarde</div>";
        }
        
        echo "</div>";
        
    } else {
        echo "<div class='error'>";
        echo "<h3>❌ Erreur d'upload</h3>";
        
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'Fichier trop volumineux (limite PHP)',
            UPLOAD_ERR_FORM_SIZE => 'Fichier trop volumineux (limite formulaire)',
            UPLOAD_ERR_PARTIAL => 'Upload partiel seulement',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier uploadé',
            UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant',
            UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire sur le disque',
            UPLOAD_ERR_EXTENSION => 'Extension PHP a arrêté l\'upload'
        ];
        
        echo "<p><strong>Code d'erreur:</strong> " . $file['error'] . "</p>";
        echo "<p><strong>Description:</strong> " . ($errors[$file['error']] ?? 'Erreur inconnue') . "</p>";
        echo "</div>";
    }
    
    echo "</div>";
}

// Formulaire de test
echo "<div class='debug'>";
echo "<h2>📤 Test d'Upload Mobile</h2>";
echo "<form method='POST' enctype='multipart/form-data'>";
echo "<p>Sélectionnez une photo depuis votre mobile :</p>";
echo "<input type='file' name='test_photo' accept='image/*,.heic,.heif' capture='environment' required>";
echo "<br><button type='submit'>🚀 Tester Upload</button>";
echo "</form>";
echo "</div>";

// Logs Laravel récents
echo "<div class='debug'>";
echo "<h2>📋 Logs Laravel Récents (Upload)</h2>";
$logFile = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $uploadLogs = array_filter(explode("\n", $logs), function($line) {
        return strpos($line, 'PHOTO') !== false || 
               strpos($line, 'upload') !== false || 
               strpos($line, 'candidature mobile') !== false;
    });
    
    if (!empty($uploadLogs)) {
        echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; font-size: 12px;'>";
        echo htmlspecialchars(implode("\n", array_slice($uploadLogs, -10))); // 10 dernières lignes
        echo "</pre>";
    } else {
        echo "<p><em>Aucun log d'upload récent trouvé</em></p>";
    }
} else {
    echo "<p><em>Fichier de log non trouvé</em></p>";
}
echo "</div>";

echo "<div class='debug'>";
echo "<h2>💡 Tips de Debug</h2>";
echo "<ul>";
echo "<li><strong>Vérifiez les logs:</strong> docker-compose logs -f app</li>";
echo "<li><strong>Console browser:</strong> F12 → Console (erreurs JavaScript)</li>";
echo "<li><strong>Network tab:</strong> F12 → Network (requêtes HTTP)</li>";
echo "<li><strong>Livewire:</strong> Vérifiez les requêtes AJAX vers /livewire/upload-file</li>";
echo "</ul>";
echo "</div>";

echo "<div class='debug'>";
echo "<p><strong>🔗 Liens utiles:</strong></p>";
echo "<p><a href='http://192.168.1.21:8080'>← Retour à l'app</a></p>";
echo "<p><a href='http://192.168.1.21:8080/register'>Test inscription candidat</a></p>";
echo "</div>";

echo "</body></html>";
?>
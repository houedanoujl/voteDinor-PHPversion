<?php
/**
 * Script de test pour la commande HEIC
 * Usage: php test-heic-command.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "🔍 Test de la commande candidates:convert-heic\n";
echo str_repeat("=", 50) . "\n";

try {
    // Test 1: Vérifier que la commande existe
    echo "1. Vérification de la commande...\n";
    $exitCode = \Illuminate\Support\Facades\Artisan::call('list');
    $output = \Illuminate\Support\Facades\Artisan::output();
    
    if (strpos($output, 'candidates:convert-heic') !== false) {
        echo "✅ Commande trouvée dans la liste\n";
    } else {
        echo "❌ Commande non trouvée dans la liste\n";
        echo "Commandes disponibles:\n";
        echo $output;
        exit(1);
    }
    
    // Test 2: Exécuter la commande avec --help
    echo "\n2. Test de l'aide...\n";
    $exitCode = \Illuminate\Support\Facades\Artisan::call('candidates:convert-heic', ['--help' => true]);
    $output = \Illuminate\Support\Facades\Artisan::output();
    echo $output;
    
    if ($exitCode === 0) {
        echo "✅ Commande d'aide exécutée avec succès\n";
    } else {
        echo "❌ Erreur lors de l'exécution de l'aide (code: {$exitCode})\n";
    }
    
    // Test 3: Dry run (sans backup ni update-db)
    echo "\n3. Test d'exécution...\n";
    $exitCode = \Illuminate\Support\Facades\Artisan::call('candidates:convert-heic');
    $output = \Illuminate\Support\Facades\Artisan::output();
    
    echo "Exit code: {$exitCode}\n";
    echo "Output:\n{$output}\n";
    
    if ($exitCode === 0) {
        echo "✅ Commande exécutée avec succès\n";
    } else {
        echo "❌ Erreur lors de l'exécution (code: {$exitCode})\n";
    }
    
} catch (\Exception $e) {
    echo "💥 Erreur: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🏁 Test terminé\n";
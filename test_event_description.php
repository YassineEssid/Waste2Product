<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\GeminiService;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Event Description Generation ===\n\n";

try {
    $geminiService = new GeminiService();

    echo "Test 1: Générer une description...\n";
    $result = $geminiService->generateEventDescription(
        'Atelier de jardinage urbain',
        'workshop',
        'Parc Central'
    );

    if ($result['success']) {
        echo "✅ Description générée avec succès !\n\n";
        echo "Description:\n";
        echo $result['description'] . "\n\n";
    } else {
        echo "❌ Erreur: " . $result['error'] . "\n\n";
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

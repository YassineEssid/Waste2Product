<?php

/**
 * Test script for AI Category Detection
 * Run with: php test_category_detection.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\CategoryDetectionService;
use App\Services\GeminiService;

echo "\n🤖 Testing AI Category Detection\n";
echo "=================================\n\n";

// Check API key
$apiKey = config('services.gemini.api_key');
if (!$apiKey || $apiKey === 'your_gemini_api_key_here') {
    echo "❌ GEMINI_API_KEY not configured!\n";
    echo "   Please set it in your .env file.\n\n";
    exit(1);
}

echo "✅ API Key configured\n\n";

// Initialize services
$gemini = new GeminiService();
$categoryService = new CategoryDetectionService($gemini);

// Test cases
$testCases = [
    "vieux fauteuil en cuir marron vintage",
    "lampe de bureau en métal années 70",
    "lot de livres de science-fiction en bon état",
    "table en bois massif pour 6 personnes",
    "vêtements enfant taille 10 ans",
    "outils de jardinage en bon état",
    "vase en céramique bleu décoratif",
];

foreach ($testCases as $index => $description) {
    $testNum = $index + 1;
    echo "📝 Test {$testNum}: \"{$description}\"\n";
    echo str_repeat("-", 70) . "\n";

    $result = $categoryService->detectCategory($description);

    if ($result['success']) {
        $data = $result['data'];

        echo "✅ Détection réussie!\n\n";
        echo "📦 Catégorie: {$data['category_label']} ({$data['category']})\n";
        echo "✏️ Titre suggéré: \"{$data['title']}\"\n";
        echo "🔧 État: {$data['condition_label']} ({$data['condition']})\n";
        echo "📊 Confiance: {$data['confidence']}%\n";

        if (!empty($data['keywords'])) {
            echo "🏷️ Mots-clés: " . implode(', ', $data['keywords']) . "\n";
        }

        if (!empty($data['reasoning'])) {
            echo "💡 Raisonnement: {$data['reasoning']}\n";
        }
    } else {
        echo "❌ Erreur: {$result['error']}\n";
    }

    echo "\n" . str_repeat("=", 70) . "\n\n";

    // Add delay to avoid rate limiting
    if ($testNum < count($testCases)) {
        echo "⏳ Pause de 2 secondes...\n\n";
        sleep(2);
    }
}

echo "🎉 Tous les tests terminés!\n\n";
echo "Pour tester dans l'interface web:\n";
echo "1. Allez sur http://127.0.0.1:8000/marketplace/create\n";
echo "2. Utilisez la boîte 'Aide IA' en haut du formulaire\n";
echo "3. Tapez une description et cliquez sur 'Détecter'\n";
echo "4. Cliquez sur 'Appliquer ces suggestions' pour remplir le formulaire\n\n";

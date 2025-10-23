<?php

/**
 * Test script for AI Price Suggestion
 * Run with: php test_price_suggestion.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\PriceSuggestionService;
use App\Services\GeminiService;

echo "\n💰 Testing AI Price Suggestion\n";
echo "================================\n\n";

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
$priceService = new PriceSuggestionService($gemini);

// Test cases
$testCases = [
    [
        'title' => 'Fauteuil Vintage en Cuir Marron',
        'description' => 'Magnifique fauteuil vintage en cuir véritable marron. Structure en bois massif, très confortable. Quelques traces d\'usage qui témoignent de son authenticité. Parfait pour un salon ou un bureau.',
        'category' => 'furniture',
        'condition' => 'good',
        'keywords' => ['vintage', 'cuir', 'fauteuil', 'marron']
    ],
    [
        'title' => 'Table à Manger Bois Massif 6 Places',
        'description' => 'Belle table en chêne massif pouvant accueillir 6 personnes. État excellent, très peu utilisée. Dimensions: 180x90cm. Idéale pour grande famille.',
        'category' => 'furniture',
        'condition' => 'excellent',
        'keywords' => ['table', 'bois', 'chêne', 'massif']
    ],
    [
        'title' => 'Lot de 20 Livres Science-Fiction',
        'description' => 'Collection de 20 livres de SF en bon état. Auteurs variés: Asimov, Herbert, Clarke, etc. Parfait pour amateur du genre.',
        'category' => 'books',
        'condition' => 'good',
        'keywords' => ['livres', 'science-fiction', 'collection']
    ],
    [
        'title' => 'Perceuse Électrique Bosch',
        'description' => 'Perceuse électrique Bosch 500W. Fonctionne parfaitement mais boîtier légèrement rayé. Vendue avec coffret et accessoires.',
        'category' => 'tools',
        'condition' => 'fair',
        'keywords' => ['perceuse', 'Bosch', 'électrique', 'bricolage']
    ],
    [
        'title' => 'Vase Céramique Bleu Artisanal',
        'description' => 'Magnifique vase en céramique bleue fait main. Hauteur 30cm. Pièce unique, aucun défaut. Parfait pour décoration moderne.',
        'category' => 'decorative',
        'condition' => 'excellent',
        'keywords' => ['vase', 'céramique', 'artisanal', 'déco']
    ]
];

foreach ($testCases as $index => $testCase) {
    $testNum = $index + 1;
    echo "💰 Test {$testNum}: \"{$testCase['title']}\"\n";
    echo str_repeat("-", 80) . "\n";
    echo "📦 Catégorie: {$testCase['category']}\n";
    echo "🔧 État: {$testCase['condition']}\n";
    echo str_repeat("-", 80) . "\n";

    $result = $priceService->suggestPrice(
        $testCase['title'],
        $testCase['description'],
        $testCase['category'],
        $testCase['condition'],
        $testCase['keywords']
    );

    if ($result['success']) {
        $data = $result['data'];

        echo "✅ Suggestion réussie!\n\n";

        // Price range
        echo "💵 PRIX SUGGÉRÉ\n";
        echo "├─ Fourchette: {$data['price_min']}€ - {$data['price_max']}€\n";
        echo "├─ Prix recommandé: {$data['recommended_price']}€ ⭐\n";
        echo "└─ Minimum négociation: {$data['negotiation_min']}€\n\n";

        // Market analysis
        $demandEmoji = $data['market_demand'] === 'forte' ? '📈' : ($data['market_demand'] === 'faible' ? '📉' : '📊');
        echo "📊 ANALYSE\n";
        echo "├─ {$demandEmoji} Demande: {$data['market_demand']}\n";
        echo "└─ 🎯 Confiance: {$data['confidence']}%\n\n";

        // Factors
        if (!empty($data['factors'])) {
            echo "💡 FACTEURS D'ÉVALUATION\n";
            foreach ($data['factors'] as $factor) {
                $sign = $factor['impact'] >= 0 ? '+' : '';
                $emoji = $factor['impact'] >= 0 ? '✅' : '⚠️';
                echo "├─ {$emoji} {$factor['name']}: {$sign}{$factor['impact']}%\n";
            }
            echo "\n";
        }

        // Reasoning
        if (!empty($data['reasoning'])) {
            echo "📝 RAISONNEMENT\n";
            echo wordwrap($data['reasoning'], 76, "\n") . "\n\n";
        }

        // Tips
        if (!empty($data['tips'])) {
            echo "💡 CONSEIL\n";
            echo wordwrap($data['tips'], 76, "\n") . "\n\n";
        }

        // Fallback indicator
        if (isset($data['is_fallback']) && $data['is_fallback']) {
            echo "ℹ️  Prix calculé automatiquement (IA non disponible)\n\n";
        }
    } else {
        echo "❌ Erreur: {$result['error']}\n\n";
    }

    echo str_repeat("=", 80) . "\n\n";

    // Add delay to avoid rate limiting
    if ($testNum < count($testCases)) {
        echo "⏳ Pause de 3 secondes...\n\n";
        sleep(3);
    }
}

echo "🎉 Tous les tests terminés!\n\n";
echo "Pour tester dans l'interface web:\n";
echo "1. Allez sur http://127.0.0.1:8000/marketplace/create\n";
echo "2. Remplissez: titre, description, catégorie, état\n";
echo "3. Cliquez sur l'icône 🧮 à côté du champ Prix\n";
echo "4. L'IA va analyser et suggérer un prix optimal\n";
echo "5. Cliquez sur 'Appliquer le prix recommandé' pour l'utiliser\n\n";

echo "💡 CONSEILS:\n";
echo "- Plus la description est détaillée, meilleure sera la suggestion\n";
echo "- Mentionnez la marque, les dimensions, les matériaux\n";
echo "- Précisez l'état réel pour un prix cohérent\n";
echo "- Vous pouvez toujours ajuster le prix suggéré\n\n";

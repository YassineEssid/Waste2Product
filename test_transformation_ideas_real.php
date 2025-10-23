<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\WasteItem;
use App\Services\GeminiService;
use App\Services\TransformationIdeasService;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Transformation Ideas Service (avec vrai WasteItem) ===\n\n";

try {
    // Trouver ou créer un waste item de test
    $wasteItem = WasteItem::where('category', 'glass')->first();

    if (!$wasteItem) {
        echo "Aucun déchet de type 'glass' trouvé. Création d'un exemple...\n";
        $wasteItem = new WasteItem();
        $wasteItem->user_id = 1;
        $wasteItem->title = "Vieilles bouteilles en verre";
        $wasteItem->description = "Lot de 20 bouteilles en verre de différentes couleurs et tailles";
        $wasteItem->category = "glass";
        $wasteItem->quantity = "20";
        $wasteItem->condition = "good";
        $wasteItem->status = "available";
        // Note: On ne sauvegarde pas pour ne pas polluer la DB
    }

    echo "Déchet testé :\n";
    echo "- Titre : {$wasteItem->title}\n";
    echo "- Catégorie : {$wasteItem->category}\n";
    echo "- Description : {$wasteItem->description}\n\n";

    // Create services
    $geminiService = new GeminiService();
    $ideasService = new TransformationIdeasService($geminiService);

    echo "Génération de 3 idées de transformation...\n\n";

    // Generate ideas
    $result = $ideasService->generateIdeas($wasteItem, 3);

    if ($result['success']) {
        echo "✅ Génération réussie !\n\n";
        echo "Nombre d'idées générées : " . count($result['ideas']) . "\n\n";

        foreach ($result['ideas'] as $index => $idea) {
            $num = $index + 1;
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "Idée #{$num}: {$idea['title']}\n";
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "Description : {$idea['description']}\n";
            echo "Difficulté : {$idea['difficulty']}\n";
            echo "Temps estimé : {$idea['estimated_time_hours']} heures\n";
            echo "Prix de vente : {$idea['selling_price_range']}\n";
            echo "Matériaux : " . implode(', ', $idea['materials_needed']) . "\n";
            echo "Impact écologique : {$idea['eco_impact']}\n";
            echo "Public cible : {$idea['target_audience']}\n\n";
        }

        echo "✅ Test terminé avec succès !\n";
    } else {
        echo "❌ Erreur lors de la génération :\n";
        echo $result['error'] . "\n\n";

        if (!empty($result['ideas'])) {
            echo "Idées de secours fournies : " . count($result['ideas']) . "\n";
            foreach ($result['ideas'] as $index => $idea) {
                echo "  " . ($index + 1) . ". " . $idea['title'] . "\n";
            }
        }
    }

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}

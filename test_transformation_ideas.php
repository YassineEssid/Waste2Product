<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\GeminiService;
use App\Services\TransformationIdeasService;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Transformation Ideas Service ===\n\n";

try {
    // Create a mock waste item
    $wasteItem = new class {
        public $id = 1;
        public $title = "Vieilles bouteilles en verre";
        public $description = "Lot de 20 bouteilles en verre de différentes couleurs et tailles";
        public $category = "glass";
        public $quantity = "20 pièces";
        public $condition = "bon état";
    };

    echo "Déchet testé :\n";
    echo "- Titre : {$wasteItem->title}\n";
    echo "- Catégorie : {$wasteItem->category}\n";
    echo "- Description : {$wasteItem->description}\n\n";

    // Create services
    $geminiService = new GeminiService();
    $ideasService = new TransformationIdeasService($geminiService);

    echo "Génération de 5 idées de transformation...\n\n";

    // Generate ideas
    $result = $ideasService->generateIdeas($wasteItem, 5);

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

        echo "Idées de secours fournies : " . count($result['ideas']) . "\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}

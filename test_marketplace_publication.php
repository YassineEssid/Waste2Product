<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transformation;
use App\Models\MarketplaceItem;
use App\Models\User;

echo "🔍 Test de publication sur Marketplace\n\n";

// Trouver une transformation "completed"
$transformation = Transformation::where('status', 'completed')->first();

if (!$transformation) {
    echo "❌ Aucune transformation avec status 'completed' trouvée.\n";
    echo "   Transformations disponibles :\n";
    $all = Transformation::all();
    foreach ($all as $t) {
        echo "   - ID: {$t->id}, Title: {$t->title}, Status: {$t->status}, Price: " . ($t->price ?? 'NULL') . "\n";
    }
    exit(1);
}

echo "✅ Transformation trouvée :\n";
echo "   ID: {$transformation->id}\n";
echo "   Title: {$transformation->title}\n";
echo "   Description: " . substr($transformation->description, 0, 50) . "...\n";
echo "   Status: {$transformation->status}\n";
echo "   Price: " . ($transformation->price ?? 'NULL') . " DT\n";
echo "   User ID: {$transformation->user_id}\n";
echo "   After images: " . (is_array($transformation->after_images) ? count($transformation->after_images) : 'NULL') . "\n\n";

// Vérifier si déjà publié
$existing = MarketplaceItem::where('seller_id', $transformation->user_id)
    ->where('name', $transformation->title)
    ->first();

if ($existing) {
    echo "⚠️  Item déjà publié sur marketplace :\n";
    echo "   ID: {$existing->id}\n";
    echo "   Name: {$existing->name}\n";
    echo "   Price: {$existing->price} DT\n\n";
} else {
    echo "📝 Création de l'item marketplace...\n";

    $marketplaceData = [
        'seller_id' => $transformation->user_id,
        'name' => $transformation->title,
        'title' => $transformation->title,
        'description' => $transformation->description,
        'price' => $transformation->price ?? 0,
        'category' => 'recycled',
        'condition' => 'new',
        'quantity' => 1,
        'status' => 'available',
    ];

    echo "   Données à insérer :\n";
    print_r($marketplaceData);

    try {
        $marketplaceItem = MarketplaceItem::create($marketplaceData);
        echo "\n✅ Item créé avec succès ! ID: {$marketplaceItem->id}\n";

        // Ajouter les images
        if ($transformation->after_images && is_array($transformation->after_images)) {
            foreach ($transformation->after_images as $imagePath) {
                $marketplaceItem->images()->create([
                    'image_path' => $imagePath
                ]);
                echo "   ✓ Image ajoutée : {$imagePath}\n";
            }
        }
    } catch (\Exception $e) {
        echo "\n❌ Erreur lors de la création : " . $e->getMessage() . "\n";
    }
}

// Vérifier tous les items marketplace
echo "\n📦 Items sur marketplace :\n";
$items = MarketplaceItem::all();
foreach ($items as $item) {
    echo "   - ID: {$item->id}, Name: {$item->name}, Price: {$item->price} DT, Seller: {$item->seller_id}\n";
}

<?php

use Illuminate\Database\Eloquent\Model;
use App\Models\MarketplaceItem;
use App\Models\User;

// Script d'urgence pour corriger le seller_id de tous les produits
// Usage : php artisan tinker < update_seller_id.php

// Remplacez par l'ID de l'utilisateur connecté ou admin
$sellerId = 1; // Par défaut, 1

MarketplaceItem::query()->update(['seller_id' => $sellerId]);

echo "Tous les produits ont maintenant seller_id = $sellerId\n";

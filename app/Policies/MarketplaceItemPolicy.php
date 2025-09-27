<?php

namespace App\Policies;

use App\Models\MarketplaceItem;
use App\Models\User;

class MarketplaceItemPolicy
{
    /**
     * Determine whether the user can update the marketplace item.
     */
    public function update(User $user, MarketplaceItem $marketplaceItem)
    {
        // Only the seller can edit their own item
        return $user->id === $marketplaceItem->seller_id;
    }

    /**
     * Determine whether the user can delete the marketplace item.
     */
    public function delete(User $user, MarketplaceItem $marketplaceItem)
    {
        // Only the seller can delete their own item
        return $user->id === $marketplaceItem->seller_id;
    }
}

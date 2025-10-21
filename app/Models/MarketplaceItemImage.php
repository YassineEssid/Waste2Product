<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketplaceItemImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'marketplace_item_id',
        'image_path',
        'order',
    ];

    public function item()
    {
        return $this->belongsTo(MarketplaceItem::class, 'marketplace_item_id');
    }
}

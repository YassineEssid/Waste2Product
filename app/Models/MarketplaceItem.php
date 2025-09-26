<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketplaceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transformation_id',
        'title',
        'description',
        'price',
        'category',
        'condition',
        'images',
        'quantity',
        'is_negotiable',
        'delivery_method',
        'delivery_notes',
        'status',
        'is_featured',
        'views_count',
        'promoted_until'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'images' => 'array',
        'is_negotiable' => 'boolean',
        'is_featured' => 'boolean',
        'promoted_until' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class); // seller
    }

    public function transformation()
    {
        return $this->belongsTo(Transformation::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('quantity', '>', 0);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePromoted($query)
    {
        return $query->where('promoted_until', '>', now());
    }

    public function scopeByPriceRange($query, $min = null, $max = null)
    {
        if ($min) {
            $query->where('price', '>=', $min);
        }
        if ($max) {
            $query->where('price', '<=', $max);
        }
        return $query;
    }

    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    public function scopeNegotiable($query)
    {
        return $query->where('is_negotiable', true);
    }

    // Accessors
    public function getFirstImageAttribute()
    {
        return $this->images && count($this->images) > 0 ? $this->images[0] : null;
    }

    public function getIsPromotedAttribute()
    {
        return $this->promoted_until && $this->promoted_until->isFuture();
    }

    public function getFormattedPriceAttribute()
    {
        return '$' . number_format((float) $this->price, 2);
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function markAsSold()
    {
        $this->update([
            'status' => 'sold',
            'quantity' => 0
        ]);
    }

    public function reserve()
    {
        $this->update(['status' => 'reserved']);
    }

    public function makeAvailable()
    {
        $this->update(['status' => 'available']);
    }

    public function promoteFor($days)
    {
        $this->update([
            'promoted_until' => now()->addDays($days),
            'is_featured' => true
        ]);
    }
}

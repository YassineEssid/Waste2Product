<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'waste_item_id',
        'title',
        'description',
        'process_description',
        'before_images',
        'after_images',
        'process_images',
        'time_spent_hours',
        'materials_cost',
        'co2_saved',
        'waste_reduced',
        'status',
        'is_featured',
        'views_count'
    ];

    protected $casts = [
        'before_images' => 'array',
        'after_images' => 'array',
        'process_images' => 'array',
        'materials_cost' => 'decimal:2',
        'co2_saved' => 'decimal:2',
        'waste_reduced' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class); // artisan
    }

    public function wasteItem()
    {
        return $this->belongsTo(WasteItem::class);
    }

    public function marketplaceItems()
    {
        return $this->hasMany(MarketplaceItem::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByArtisan($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Mutators
    public function publish()
    {
        $this->update([
            'status' => 'published'
        ]);

        // Update waste item status
        $this->wasteItem->update([
            'status' => 'transformed'
        ]);
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    // Accessors
    public function getTotalEnvironmentalImpactAttribute()
    {
        return [
            'co2_saved' => $this->co2_saved,
            'waste_reduced' => $this->waste_reduced,
            'materials_cost' => $this->materials_cost
        ];
    }

    public function getFirstBeforeImageAttribute()
    {
        return $this->before_images && count($this->before_images) > 0 ? $this->before_images[0] : null;
    }

    public function getFirstAfterImageAttribute()
    {
        return $this->after_images && count($this->after_images) > 0 ? $this->after_images[0] : null;
    }
}

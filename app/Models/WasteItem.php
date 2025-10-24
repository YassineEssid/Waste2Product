<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
         'quantity',
        'condition',
        'location_address',
        'location_lat',
        'location_lng',
        'images',
        'is_available',
        'status'
    ];

    protected $casts = [
        'images' => 'array',
        'is_available' => 'boolean',
        'location_lat' => 'decimal:8',
        'location_lng' => 'decimal:8',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function repairRequests()
    {
        return $this->hasMany(RepairRequest::class);
    }

    public function transformations()
    {
        return $this->hasMany(Transformation::class);
    }

    public function activeRepairRequest()
    {
        return $this->hasOne(RepairRequest::class)->whereIn('status', ['waiting', 'assigned', 'in_progress']);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_available', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeNearLocation($query, $lat, $lng, $radius = 10)
    {
        return $query->whereNotNull('location_lat')
            ->whereNotNull('location_lng')
            ->whereRaw("
                6371 * acos(
                    cos(radians(?)) * cos(radians(location_lat)) *
                    cos(radians(location_lng) - radians(?)) +
                    sin(radians(?)) * sin(radians(location_lat))
                ) < ?
            ", [$lat, $lng, $lat, $radius]);
    }

    // Accessors
    public function getFirstImageAttribute()
    {
        return $this->images && count($this->images) > 0 ? $this->images[0] : null;
    }

    /**
     * Get the category model for this waste item
     * Note: category column is stored as a string (name), not a foreign key
     * We use categoryModel() to avoid conflict with the category attribute
     */
    public function categoryModel()
    {
        return $this->belongsTo(Category::class, 'category', 'name');
    }
}

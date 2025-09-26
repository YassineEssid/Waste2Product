<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'starts_at',
        'ends_at',
        'location_address',
        'location_lat',
        'location_lng',
        'is_online',
        'meeting_link',
        'max_participants',
        'price',
        'images',
        'requirements',
        'status',
        'is_featured'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'location_lat' => 'decimal:8',
        'location_lng' => 'decimal:8',
        'is_online' => 'boolean',
        'price' => 'decimal:2',
        'images' => 'array',
        'is_featured' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class); // event creator
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_registrations')
            ->withPivot('status', 'special_requirements', 'registered_at', 'attended_at')
            ->withTimestamps();
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>', now())->where('status', 'published');
    }

    public function scopePast($query)
    {
        return $query->where('ends_at', '<', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNearLocation($query, $lat, $lng, $radius = 25)
    {
        return $query->where('is_online', false)
            ->whereNotNull('location_lat')
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
    public function getAvailableSpotsAttribute()
    {
        if (!$this->max_participants) {
            return null;
        }
        
        $registeredCount = $this->registrations()
            ->whereIn('status', ['registered', 'confirmed', 'attended'])
            ->count();
            
        return max(0, $this->max_participants - $registeredCount);
    }

    public function getIsFullAttribute()
    {
        return $this->max_participants && $this->available_spots === 0;
    }

    public function getIsPastAttribute()
    {
        return $this->ends_at->isPast();
    }

    public function getIsUpcomingAttribute()
    {
        return $this->starts_at->isFuture();
    }

    public function getFirstImageAttribute()
    {
        return $this->images && count($this->images) > 0 ? $this->images[0] : null;
    }

    // Methods
    public function canRegister()
    {
        return $this->status === 'published' && 
               $this->is_upcoming && 
               (!$this->is_full);
    }

    public function getTotalAttendees()
    {
        return $this->registrations()
            ->whereIn('status', ['confirmed', 'attended'])
            ->count();
    }
}

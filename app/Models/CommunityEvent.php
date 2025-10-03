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
        'image',
        'location',
        'starts_at',
        'ends_at',
        'status'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    // Accessors pour simuler les données manquantes
    public function getCreatorNameAttribute()
    {
        return 'Community Organizer';
    }

    public function getLocationAttribute()
    {
        return 'Location not specified';
    }

    public function getAttendeesCountAttribute()
    {
        return 0;
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('ends_at', '<', now());
    }

    public function scopeOngoing($query)
    {
        return $query->where('starts_at', '<=', now())
                    ->where('ends_at', '>', now());
    }

    // Accessors pour le statut
    public function getIsPastAttribute()
    {
        return $this->ends_at->isPast();
    }

    public function getIsUpcomingAttribute()
    {
        return $this->starts_at->isFuture();
    }

    public function getIsOngoingAttribute()
    {
        return $this->starts_at->isPast() && $this->ends_at->isFuture();
    }

    // Méthode pour obtenir le statut actuel
    public function getCurrentStatusAttribute()
    {
        if ($this->is_past) {
            return 'completed';
        } elseif ($this->is_ongoing) {
            return 'ongoing';
        } else {
            return 'upcoming';
        }
    }
}
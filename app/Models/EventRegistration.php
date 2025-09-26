<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'community_event_id',
        'status',
        'special_requirements',
        'registered_at',
        'attended_at'
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'attended_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(CommunityEvent::class, 'community_event_id');
    }

    // Scopes
    public function scopeRegistered($query)
    {
        return $query->where('status', 'registered');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeAttended($query)
    {
        return $query->where('status', 'attended');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForEvent($query, $eventId)
    {
        return $query->where('community_event_id', $eventId);
    }

    // Methods
    public function confirm()
    {
        $this->update(['status' => 'confirmed']);
    }

    public function markAsAttended()
    {
        $this->update([
            'status' => 'attended',
            'attended_at' => now()
        ]);
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'badge_id',
        'earned_at',
        'is_displayed',
        'progress',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
        'is_displayed' => 'boolean',
        'progress' => 'integer',
    ];

    /**
     * Get the user that owns this badge
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the badge details
     */
    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    /**
     * Scope to get earned badges only
     */
    public function scopeEarned($query)
    {
        return $query->whereNotNull('earned_at');
    }

    /**
     * Scope to get displayed badges only
     */
    public function scopeDisplayed($query)
    {
        return $query->where('is_displayed', true);
    }

    /**
     * Scope to get in-progress badges
     */
    public function scopeInProgress($query)
    {
        return $query->whereNull('earned_at');
    }

    /**
     * Check if badge is earned
     */
    public function isEarned()
    {
        return !is_null($this->earned_at);
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->isEarned()) {
            return 100;
        }

        if ($this->badge && $this->badge->required_count > 0) {
            return min(100, ($this->progress / $this->badge->required_count) * 100);
        }

        return 0;
    }

    /**
     * Get time since earned
     */
    public function getTimeSinceEarnedAttribute()
    {
        if ($this->earned_at) {
            return $this->earned_at->diffForHumans();
        }
        return null;
    }
}

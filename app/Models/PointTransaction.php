<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
        'type',
        'description',
        'reference_type',
        'reference_id',
        'balance_after',
    ];

    protected $casts = [
        'points' => 'integer',
        'reference_id' => 'integer',
        'balance_after' => 'integer',
    ];

    /**
     * Get the user that owns this transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reference model (polymorphic)
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * Scope to get positive transactions (earned points)
     */
    public function scopeEarned($query)
    {
        return $query->where('points', '>', 0);
    }

    /**
     * Scope to get negative transactions (spent points)
     */
    public function scopeSpent($query)
    {
        return $query->where('points', '<', 0);
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get recent transactions
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get formatted points
     */
    public function getFormattedPointsAttribute()
    {
        if ($this->points > 0) {
            return '+' . $this->points;
        }
        return $this->points;
    }

    /**
     * Get transaction color based on points
     */
    public function getColorAttribute()
    {
        return $this->points > 0 ? 'success' : 'danger';
    }

    /**
     * Get icon based on type
     */
    public function getIconAttribute()
    {
        return match($this->type) {
            'event_attended' => 'fa-calendar-check',
            'event_created' => 'fa-calendar-plus',
            'comment_posted' => 'fa-comment',
            'comment_liked' => 'fa-thumbs-up',
            'registration' => 'fa-user-plus',
            'login_streak' => 'fa-fire',
            'achievement' => 'fa-trophy',
            default => 'fa-star'
        };
    }

    /**
     * Get time ago
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}

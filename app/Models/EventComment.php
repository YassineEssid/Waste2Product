<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_event_id',
        'user_id',
        'comment',
        'rating',
        'is_approved',
        'commented_at'
    ];

    protected $casts = [
        'commented_at' => 'datetime',
        'is_approved' => 'boolean',
    ];

    /**
     * Get the event that owns the comment.
     */
    public function event()
    {
        return $this->belongsTo(CommunityEvent::class, 'community_event_id');
    }

    /**
     * Get the user that created the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only approved comments.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope to get comments for a specific event.
     */
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('community_event_id', $eventId);
    }

    /**
     * Scope to get comments by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get comments with a specific rating.
     */
    public function scopeWithRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Get the star rating as HTML.
     */
    public function getStarRatingHtmlAttribute()
    {
        if (!$this->rating) {
            return '';
        }

        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $html .= '<i class="fas fa-star text-yellow-400"></i>';
            } else {
                $html .= '<i class="far fa-star text-gray-300"></i>';
            }
        }
        return $html;
    }

    /**
     * Get the time ago format.
     */
    public function getTimeAgoAttribute()
    {
        return $this->commented_at->diffForHumans();
    }

    /**
     * Get the formatted date.
     */
    public function getFormattedDateAttribute()
    {
        return $this->commented_at->format('d M Y à H:i');
    }
}

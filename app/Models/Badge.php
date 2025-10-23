<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'image',
        'color',
        'type',
        'required_points',
        'required_count',
        'requirement_type',
        'is_active',
        'rarity',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'required_points' => 'integer',
        'required_count' => 'integer',
        'rarity' => 'integer',
    ];

    /**
     * Get users who have this badge
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
                    ->withTimestamps()
                    ->withPivot(['earned_at', 'is_displayed', 'progress']);
    }

    /**
     * Get user badges records
     */
    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    /**
     * Scope to get only active badges
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by rarity
     */
    public function scopeByRarity($query, $rarity)
    {
        return $query->where('rarity', $rarity);
    }

    /**
     * Get rarity name
     */
    public function getRarityNameAttribute()
    {
        return match($this->rarity) {
            1 => 'Common',
            2 => 'Rare',
            3 => 'Epic',
            4 => 'Legendary',
            default => 'Unknown'
        };
    }

    /**
     * Get rarity color
     */
    public function getRarityColorAttribute()
    {
        return match($this->rarity) {
            1 => '#6c757d', // Gray
            2 => '#0d6efd', // Blue
            3 => '#9b59b6', // Purple
            4 => '#f39c12', // Gold
            default => '#6c757d'
        };
    }

    /**
     * Get icon HTML
     */
    public function getIconHtmlAttribute()
    {
        if ($this->icon) {
            return '<i class="' . $this->icon . '" style="color: ' . $this->color . ';"></i>';
        }
        return '<i class="fas fa-award" style="color: ' . $this->color . ';"></i>';
    }

    /**
     * Get formatted description
     */
    public function getFormattedDescriptionAttribute()
    {
        return str_replace(
            ['{required_count}', '{required_points}'],
            [$this->required_count, $this->required_points],
            $this->description
        );
    }

    /**
     * Check if user has earned this badge
     */
    public function isEarnedBy(User $user)
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Get progress percentage for a user
     */
    public function getProgressFor(User $user)
    {
        $userBadge = $this->userBadges()->where('user_id', $user->id)->first();

        if (!$userBadge) {
            return 0;
        }

        if ($userBadge->earned_at) {
            return 100;
        }

        if ($this->required_count > 0) {
            return min(100, ($userBadge->progress / $this->required_count) * 100);
        }

        return 0;
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'bio',
        'avatar',
        'location_lat',
        'location_lng',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'location_lat' => 'float',
            'location_lng' => 'float',
        ];
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is repairer
     */
    public function isRepairer(): bool
    {
        return $this->hasRole('repairer');
    }

    /**
     * Check if user is artisan
     */
    public function isArtisan(): bool
    {
        return $this->hasRole('artisan');
    }

    // Relationships
    public function wasteItems()
    {
        return $this->hasMany(WasteItem::class);
    }

    public function repairRequests()
    {
        return $this->hasMany(RepairRequest::class);
    }

    public function assignedRepairs()
    {
        return $this->hasMany(RepairRequest::class, 'repairer_id');
    }

    public function transformations()
    {
        return $this->hasMany(Transformation::class);
    }

    public function events()
    {
        return $this->hasMany(CommunityEvent::class);
    }

    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function marketplaceItems()
    {
        return $this->hasMany(MarketplaceItem::class, 'seller_id');
    }

    public function eventComments()
    {
        return $this->hasMany(EventComment::class);
    }

    // Gamification Relationships
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withTimestamps()
                    ->withPivot(['earned_at', 'is_displayed', 'progress']);
    }

    public function earnedBadges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->whereNotNull('user_badges.earned_at')
                    ->withTimestamps()
                    ->withPivot(['earned_at', 'is_displayed', 'progress']);
    }

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    // Gamification Methods
    public function getLevelTitleAttribute()
    {
        return match(true) {
            $this->current_level >= 50 => 'Eco Legend',
            $this->current_level >= 40 => 'Sustainability Master',
            $this->current_level >= 30 => 'Green Champion',
            $this->current_level >= 20 => 'Environmental Expert',
            $this->current_level >= 10 => 'Eco Enthusiast',
            $this->current_level >= 5 => 'Green Contributor',
            default => 'Eco Beginner'
        };
    }

    public function getProgressToNextLevelAttribute()
    {
        if ($this->points_to_next_level <= 0) {
            return 100;
        }
        $pointsInCurrentLevel = $this->total_points % $this->points_to_next_level;
        return ($pointsInCurrentLevel / $this->points_to_next_level) * 100;
    }

    public function hasBadge($badgeSlug)
    {
        return $this->earnedBadges()->where('slug', $badgeSlug)->exists();
    }
}


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
            'location_lat' => 'decimal:8',
            'location_lng' => 'decimal:8',
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
        return $this->hasMany(MarketplaceItem::class);
    }
}

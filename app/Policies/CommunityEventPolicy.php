<?php

namespace App\Policies;

use App\Models\CommunityEvent;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommunityEventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any community events.
     */
    public function viewAny(User $user)
    {
        return true; // Allow all authenticated users to view events
    }

    /**
     * Determine whether the user can view the community event.
     */
    public function view(User $user, CommunityEvent $event)
    {
        return true; // Allow all authenticated users to view event details
    }

    /**
     * Determine whether the user can create community events.
     */
    public function create(User $user)
    {
        return true; // Allow all authenticated users to create events
    }

    /**
     * Determine whether the user can update the community event.
     */
    public function update(User $user, CommunityEvent $event)
    {
        return $user->id === $event->user_id;
    }

    /**
     * Determine whether the user can delete the community event.
     */
    public function delete(User $user, CommunityEvent $event)
    {
        return $user->id === $event->user_id;
    }
}

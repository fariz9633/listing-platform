<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ListingPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Listing $listing): bool
    {
        if ($listing->isApproved()) {
            return true;
        }
        if ($user && $listing->user_id === $user->id) {
            return true;
        }

        if ($user && $user->isAdmin()) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isProvider() && $user->is_active;
    }

    public function update(User $user, Listing $listing): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->isProvider() && $listing->user_id === $user->id) {
            return !$listing->isPendingModeration() && !$listing->isSuspended();
        }

        return false;
    }

    public function delete(User $user, Listing $listing): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isProvider() && $listing->user_id === $user->id) {
            return $listing->isDraft();
        }

        return false;
    }

    public function submit(User $user, Listing $listing): bool
    {
        return $user->isProvider() 
            && $listing->user_id === $user->id 
            && $listing->isDraft();
    }

    public function moderate(User $user, Listing $listing): bool
    {
        return $user->isAdmin() && $listing->isPendingModeration();
    }

    public function approve(User $user, Listing $listing): bool
    {
        return $user->isAdmin() && $listing->canTransitionTo(Listing::STATUS_APPROVED);
    }

    public function reject(User $user, Listing $listing): bool
    {
        return $user->isAdmin() && $listing->canTransitionTo(Listing::STATUS_REJECTED);
    }

    public function suspend(User $user, Listing $listing): bool
    {
        return $user->isAdmin() && $listing->canTransitionTo(Listing::STATUS_SUSPENDED);
    }
}


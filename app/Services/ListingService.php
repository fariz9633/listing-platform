<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\User;
use App\Models\ModerationLog;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ListingService
{
    public function createListing(User $user, array $data): Listing
    {
        
        if (!$user->isProvider()) {
            throw new InvalidArgumentException('Only providers can create listings');
        }

        return DB::transaction(function () use ($user, $data) {
            $listing = new Listing($data);
            $listing->user_id = $user->id;
            $listing->status = Listing::STATUS_DRAFT;
            $listing->save();

            return $listing;
        });
    }

    public function updateListing(Listing $listing, array $data): Listing
    {
        
        if ($listing->isPendingModeration() || $listing->isSuspended()) {
            throw new InvalidArgumentException('Cannot update listing in current status');
        }

        return DB::transaction(function () use ($listing, $data) {
            $listing->update($data);
            return $listing->fresh();
        });
    }

    public function submitForModeration(Listing $listing): Listing
    {
        if (!$listing->canTransitionTo(Listing::STATUS_PENDING_MODERATION)) {
            throw new InvalidArgumentException('Cannot submit listing for moderation from current status');
        }

        return DB::transaction(function () use ($listing) {
            $listing->status = Listing::STATUS_PENDING_MODERATION;
            $listing->submitted_at = now();
            $listing->save();

            ModerationLog::create([
                'listing_id' => $listing->id,
                'action' => 'submitted',
                'from_status' => Listing::STATUS_DRAFT,
                'to_status' => Listing::STATUS_PENDING_MODERATION,
            ]);

            return $listing;
        });
    }

    public function approveListing(Listing $listing, User $admin, ?string $notes = null): Listing
    {
        if (!$admin->isAdmin()) {
            throw new InvalidArgumentException('Only admins can approve listings');
        }

        if (!$listing->canTransitionTo(Listing::STATUS_APPROVED)) {
            throw new InvalidArgumentException('Cannot approve listing from current status');
        }

        return DB::transaction(function () use ($listing, $admin, $notes) {
            $oldStatus = $listing->status;
            
            $listing->status = Listing::STATUS_APPROVED;
            $listing->moderated_at = now();
            $listing->moderated_by = $admin->id;
            $listing->moderation_notes = $notes;
            $listing->save();

            ModerationLog::create([
                'listing_id' => $listing->id,
                'user_id' => $admin->id,
                'action' => 'approved',
                'from_status' => $oldStatus,
                'to_status' => Listing::STATUS_APPROVED,
                'notes' => $notes,
            ]);

            return $listing;
        });
    }

    public function rejectListing(Listing $listing, User $admin, string $reason, ?string $notes = null): Listing
    {
        if (!$admin->isAdmin()) {
            throw new InvalidArgumentException('Only admins can reject listings');
        }

        if (!$listing->canTransitionTo(Listing::STATUS_REJECTED)) {
            throw new InvalidArgumentException('Cannot reject listing from current status');
        }

        return DB::transaction(function () use ($listing, $admin, $reason, $notes) {
            $oldStatus = $listing->status;
            
            $listing->status = Listing::STATUS_REJECTED;
            $listing->moderated_at = now();
            $listing->moderated_by = $admin->id;
            $listing->rejection_reason = $reason;
            $listing->moderation_notes = $notes;
            $listing->save();

            ModerationLog::create([
                'listing_id' => $listing->id,
                'user_id' => $admin->id,
                'action' => 'rejected',
                'from_status' => $oldStatus,
                'to_status' => Listing::STATUS_REJECTED,
                'reason' => $reason,
                'notes' => $notes,
            ]);

            return $listing;
        });
    }

    public function suspendListing(Listing $listing, User $admin, string $reason, ?string $notes = null): Listing
    {
        if (!$admin->isAdmin()) {
            throw new InvalidArgumentException('Only admins can suspend listings');
        }

        if (!$listing->canTransitionTo(Listing::STATUS_SUSPENDED)) {
            throw new InvalidArgumentException('Cannot suspend listing from current status');
        }

        return DB::transaction(function () use ($listing, $admin, $reason, $notes) {
            $oldStatus = $listing->status;

            $listing->status = Listing::STATUS_SUSPENDED;
            $listing->moderated_at = now();
            $listing->moderated_by = $admin->id;
            $listing->moderation_notes = $notes;
            $listing->save();

            ModerationLog::create([
                'listing_id' => $listing->id,
                'user_id' => $admin->id,
                'action' => 'suspended',
                'from_status' => $oldStatus,
                'to_status' => Listing::STATUS_SUSPENDED,
                'reason' => $reason,
                'notes' => $notes,
            ]);

            return $listing;
        });
    }

    public function incrementViewCount(Listing $listing): void
    {
        
        $listing->increment('view_count');
    }

    public function incrementEnquiryCount(Listing $listing): void
    {
        $listing->increment('enquiry_count');
    }
}

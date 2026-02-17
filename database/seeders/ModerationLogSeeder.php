<?php

namespace Database\Seeders;

use App\Models\ModerationLog;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;

class ModerationLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::where('role', 'admin')->first();
        $moderatedListings = Listing::whereIn('status', ['approved', 'rejected', 'suspended'])->get();
        
        $approvalNotes = [
            'Content meets quality standards',
            'Verified provider information',
            'All requirements satisfied',
            'Approved after review',
            null, // Some approvals have no notes
        ];

        $rejectionReasons = [
            'Insufficient information provided',
            'Content does not meet quality standards',
            'Missing required details',
            'Inappropriate content detected',
            'Duplicate listing',
        ];

        $suspensionReasons = [
            'Multiple user complaints received',
            'Violation of terms of service',
            'Suspicious activity detected',
            'Provider verification failed',
        ];

        $logCount = 0;

        foreach ($moderatedListings as $listing) {
            // Create submission log
            ModerationLog::create([
                'listing_id' => $listing->id,
                'user_id' => $listing->user_id,
                'action' => 'submitted',
                'from_status' => 'draft',
                'to_status' => 'pending_moderation',
                'notes' => 'Listing submitted for moderation',
                'created_at' => $listing->submitted_at ?? now()->subDays(rand(1, 30)),
            ]);
            $logCount++;

            // Create moderation decision log
            if ($listing->status === 'approved') {
                ModerationLog::create([
                    'listing_id' => $listing->id,
                    'user_id' => $admin->id,
                    'action' => 'approved',
                    'from_status' => 'pending_moderation',
                    'to_status' => 'approved',
                    'notes' => $approvalNotes[array_rand($approvalNotes)],
                    'created_at' => $listing->moderated_at ?? now()->subDays(rand(0, 25)),
                ]);
                $logCount++;
            } elseif ($listing->status === 'rejected') {
                ModerationLog::create([
                    'listing_id' => $listing->id,
                    'user_id' => $admin->id,
                    'action' => 'rejected',
                    'from_status' => 'pending_moderation',
                    'to_status' => 'rejected',
                    'reason' => $rejectionReasons[array_rand($rejectionReasons)],
                    'notes' => 'Reviewed and rejected',
                    'created_at' => $listing->moderated_at ?? now()->subDays(rand(0, 25)),
                ]);
                $logCount++;
            } elseif ($listing->status === 'suspended') {
                // First approved
                ModerationLog::create([
                    'listing_id' => $listing->id,
                    'user_id' => $admin->id,
                    'action' => 'approved',
                    'from_status' => 'pending_moderation',
                    'to_status' => 'approved',
                    'notes' => 'Initially approved',
                    'created_at' => now()->subDays(rand(10, 30)),
                ]);
                $logCount++;

                // Then suspended
                ModerationLog::create([
                    'listing_id' => $listing->id,
                    'user_id' => $admin->id,
                    'action' => 'suspended',
                    'from_status' => 'approved',
                    'to_status' => 'suspended',
                    'reason' => $suspensionReasons[array_rand($suspensionReasons)],
                    'notes' => 'Suspended after review',
                    'created_at' => $listing->moderated_at ?? now()->subDays(rand(0, 5)),
                ]);
                $logCount++;
            }
        }

        $this->command->info("Created {$logCount} moderation logs");
    }
}


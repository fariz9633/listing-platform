<?php

namespace Database\Seeders;

use App\Models\Enquiry;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnquirySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $approvedListings = Listing::where('status', 'approved')->get();
        $guests = User::where('role', 'guest')->get();
        
        $names = [
            'John Smith', 'Michael Brown', 'David Wilson', 'James Taylor', 'Robert Anderson',
            'William Thomas', 'Richard Jackson', 'Joseph White', 'Charles Harris', 'Thomas Martin'
        ];
        
        $messages = [
            'Hi, I\'m interested in your services. Are you available this weekend? Please let me know your rates and availability.',
            'Hello, I would like to book an appointment. What are your available times this week?',
            'I saw your listing and I\'m very interested. Could you please provide more details about your services?',
            'Are you available for outcall services? I\'m located in the CBD area.',
            'Hi there, I\'m looking to book a session. What is your hourly rate and what does it include?',
            'Hello, I\'m interested in booking an extended appointment. Do you offer overnight services?',
            'I would like to inquire about your availability for next week. Please contact me at your earliest convenience.',
            'Hi, I\'m a regular client looking for a reliable provider. Are you available for regular bookings?',
            'Hello, I saw your profile and I\'m very impressed. Could we arrange a meeting to discuss services?',
            'I\'m interested in your massage services. Do you have any availability this evening?',
        ];

        $ipAddresses = [
            '203.45.67.89', '101.23.45.67', '58.123.45.67', '124.56.78.90',
            '180.234.56.78', '27.123.45.67', '49.234.56.78', '110.45.67.89'
        ];

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15',
            'Mozilla/5.0 (iPad; CPU OS 14_6 like Mac OS X) AppleWebKit/605.1.15',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
        ];

        $enquiryCount = 0;

        foreach ($approvedListings as $listing) {
            // Each approved listing gets 0-5 enquiries
            $numEnquiries = rand(0, 5);
            
            for ($i = 0; $i < $numEnquiries; $i++) {
                $guest = $guests->random();
                $name = $names[array_rand($names)];
                $isSpam = rand(1, 100) <= 5; // 5% spam rate
                $isRead = rand(0, 1) ? true : false;

                Enquiry::create([
                    'listing_id' => $listing->id,
                    'user_id' => rand(0, 1) ? $guest->id : null, // 50% authenticated, 50% guest
                    'name' => $name,
                    'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
                    'phone' => '04' . rand(10000000, 99999999),
                    'message' => $messages[array_rand($messages)],
                    'ip_address' => $ipAddresses[array_rand($ipAddresses)],
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'session_id' => 'sess_' . \Illuminate\Support\Str::random(32),
                    'status' => $isSpam ? 'spam' : ($isRead ? 'read' : 'pending'),
                    'is_spam' => $isSpam,
                    'spam_reason' => $isSpam ? 'Suspicious pattern detected' : null,
                    'read_at' => $isRead && !$isSpam ? now()->subDays(rand(0, 20)) : null,
                    'created_at' => now()->subDays(rand(0, 30)),
                ]);

                $enquiryCount++;
            }
        }

        // Create some spam enquiries
        for ($i = 0; $i < 10; $i++) {
            $listing = $approvedListings->random();

            Enquiry::create([
                'listing_id' => $listing->id,
                'user_id' => null,
                'name' => 'Spam Bot ' . $i,
                'email' => 'spam' . $i . '@temporary-mail.com',
                'phone' => null,
                'message' => 'CLICK HERE FOR FREE STUFF!!! Visit our website now!!!',
                'ip_address' => '192.168.1.' . rand(1, 255),
                'user_agent' => 'SpamBot/1.0',
                'session_id' => 'spam_' . \Illuminate\Support\Str::random(32),
                'status' => 'spam',
                'is_spam' => true,
                'spam_reason' => 'Spam keywords detected',
                'read_at' => null,
                'created_at' => now()->subDays(rand(0, 10)),
            ]);

            $enquiryCount++;
        }

        $this->command->info("Created {$enquiryCount} enquiries (including spam)");
    }
}


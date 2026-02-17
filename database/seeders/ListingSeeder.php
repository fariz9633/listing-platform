<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $providers = User::where('role', 'provider')->get();
        
        $categories = ['escort', 'massage', 'adult-entertainment', 'companionship', 'dating', 'adult-services'];
        $cities = ['Sydney', 'Melbourne', 'Brisbane', 'Perth', 'Adelaide', 'Gold Coast', 'Canberra'];
        $suburbs = [
            'Sydney' => ['CBD', 'Bondi', 'Parramatta', 'Manly', 'Surry Hills'],
            'Melbourne' => ['CBD', 'St Kilda', 'Richmond', 'Fitzroy', 'South Yarra'],
            'Brisbane' => ['CBD', 'Fortitude Valley', 'South Bank', 'New Farm', 'West End'],
            'Perth' => ['CBD', 'Fremantle', 'Subiaco', 'Northbridge', 'Cottesloe'],
            'Adelaide' => ['CBD', 'Glenelg', 'North Adelaide', 'Norwood', 'Unley'],
            'Gold Coast' => ['Surfers Paradise', 'Broadbeach', 'Burleigh Heads', 'Southport', 'Main Beach'],
            'Canberra' => ['CBD', 'Civic', 'Braddon', 'Kingston', 'Manuka'],
        ];

        $titles = [
            'Professional Companion Available',
            'Relaxing Massage Services',
            'Elite Entertainment Services',
            'Premium Companionship',
            'Luxury Services Available',
            'Exclusive Adult Services',
            'High-Class Companion',
            'Therapeutic Massage',
            'VIP Entertainment',
            'Discreet Services',
        ];

        $descriptions = [
            'Offering professional and discreet services. Available for incall and outcall appointments. Please contact for rates and availability.',
            'Experienced provider offering premium services in a clean and comfortable environment. Serious inquiries only.',
            'High-quality services with attention to detail. Professional, friendly, and discreet. Available by appointment.',
            'Luxury companionship services for discerning clients. Outcall available to hotels and private residences.',
            'Relaxing and therapeutic services in a private setting. Clean, professional, and respectful environment.',
            'Elite services for professional gentlemen. Discreet, reliable, and always punctual. References available.',
            'Premium adult entertainment services. Available for social events and private appointments.',
            'Experienced and professional provider. Offering a range of services to suit your needs. Contact for details.',
            'Exclusive services in upscale locations. Professional presentation and excellent service guaranteed.',
            'Discreet and professional services. Available for short and extended bookings. Serious inquiries welcome.',
        ];

        $statuses = ['draft', 'pending_moderation', 'approved', 'rejected', 'suspended'];
        $statusWeights = [10, 15, 60, 10, 5]; // 60% approved, 15% pending, etc.

        $listingCount = 0;

        foreach ($providers as $provider) {
            // Each provider creates 3-8 listings
            $numListings = rand(3, 8);
            
            for ($i = 0; $i < $numListings; $i++) {
                $city = $cities[array_rand($cities)];
                $suburb = $suburbs[$city][array_rand($suburbs[$city])];
                $category = $categories[array_rand($categories)];
                $title = $titles[array_rand($titles)] . ' - ' . $city;
                $description = $descriptions[array_rand($descriptions)] . "\n\n" . $descriptions[array_rand($descriptions)];
                
                // Weighted random status
                $status = $this->getWeightedRandomStatus($statuses, $statusWeights);
                
                $pricingType = rand(0, 1) ? 'hourly' : 'fixed';
                
                $listing = Listing::create([
                    'user_id' => $provider->id,
                    'title' => $title,
                    'slug' => Str::slug($title) . '-' . Str::random(6),
                    'description' => $description,
                    'category' => $category,
                    'city' => $city,
                    'suburb' => $suburb,
                    'pricing_type' => $pricingType,
                    'price' => $pricingType === 'hourly' ? rand(150, 500) : rand(200, 1000),
                    'price_min' => rand(100, 300),
                    'price_max' => rand(400, 800),
                    'status' => $status,
                    'view_count' => $status === 'approved' ? rand(10, 500) : 0,
                    'enquiry_count' => $status === 'approved' ? rand(0, 50) : 0,
                    'meta_title' => $title . ' | Adult Services',
                    'meta_description' => Str::limit($description, 150),
                    'submitted_at' => in_array($status, ['pending_moderation', 'approved', 'rejected', 'suspended']) ? now()->subDays(rand(1, 30)) : null,
                    'moderated_at' => in_array($status, ['approved', 'rejected', 'suspended']) ? now()->subDays(rand(0, 25)) : null,
                    'rejection_reason' => $status === 'rejected' ? 'Content does not meet quality standards. Please provide more detailed information.' : null,
                    'moderation_notes' => in_array($status, ['rejected', 'suspended']) ? 'Reviewed by moderation team' : null,
                ]);

                $listingCount++;
            }
        }

        $this->command->info("Created {$listingCount} listings");
    }

    /**
     * Get weighted random status
     */
    private function getWeightedRandomStatus($statuses, $weights)
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($statuses as $index => $status) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $status;
            }
        }
        
        return $statuses[0];
    }
}


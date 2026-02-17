<?php

namespace App\Services;

use App\Models\Enquiry;
use App\Models\Listing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class EnquiryService
{
    const RATE_LIMIT_IP_MAX = 3;
    const RATE_LIMIT_IP_WINDOW = 3600; 

    const RATE_LIMIT_EMAIL_MAX = 5;
    const RATE_LIMIT_EMAIL_WINDOW = 86400; 

    public function createEnquiry(Listing $listing, array $data, string $ipAddress, ?string $userAgent = null): Enquiry
    {
        if (!$listing->isApproved()) {
            throw new InvalidArgumentException('Cannot enquire about this listing');
        }

        $this->checkRateLimits($data['email'], $ipAddress);

        $isSpam = $this->detectSpam($data['message'], $data['email']);

        return DB::transaction(function () use ($listing, $data, $ipAddress, $userAgent, $isSpam) {
            $enquiry = Enquiry::create([
                'listing_id' => $listing->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'message' => $data['message'],
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'session_id' => session()->getId(),
                'is_spam' => $isSpam,
                'status' => $isSpam ? Enquiry::STATUS_SPAM : Enquiry::STATUS_PENDING,
            ]);

            if (!$isSpam) {
                app(ListingService::class)->incrementEnquiryCount($listing);
            }

            return $enquiry;
        });
    }

    protected function checkRateLimits(string $email, string $ipAddress): void
    {
        $ipKey = "enquiry_rate_limit:ip:{$ipAddress}";
        $ipCount = Cache::get($ipKey, 0);
        
        if ($ipCount >= self::RATE_LIMIT_IP_MAX) {
            throw new InvalidArgumentException('Too many enquiries from your IP address. Please try again later.');
        }

        $emailKey = "enquiry_rate_limit:email:{$email}";
        $emailCount = Cache::get($emailKey, 0);
        
        if ($emailCount >= self::RATE_LIMIT_EMAIL_MAX) {
            throw new InvalidArgumentException('Too many enquiries from this email address. Please try again later.');
        }

        Cache::put($ipKey, $ipCount + 1, self::RATE_LIMIT_IP_WINDOW);
        Cache::put($emailKey, $emailCount + 1, self::RATE_LIMIT_EMAIL_WINDOW);
    }

    protected function detectSpam(string $message, string $email): bool
    {
        $spamKeywords = ['viagra', 'cialis', 'casino', 'lottery', 'winner', 'click here', 'buy now'];
        
        $messageLower = strtolower($message);
        foreach ($spamKeywords as $keyword) {
            if (str_contains($messageLower, $keyword)) {
                return true;
            }
        }

        if (preg_match('/[0-9]{5,}/', $email)) {
            return true; 
        }

        $messageLength = strlen($message);
        if ($messageLength < 10 || $messageLength > 5000) {
            return true;
        }

        $urlCount = substr_count($messageLower, 'http');
        if ($urlCount > 2) {
            return true;
        }

        return false;
    }

    public function markAsSpam(Enquiry $enquiry, string $reason): Enquiry
    {
        $enquiry->markAsSpam($reason);
        return $enquiry;
    }

    public function markAsRead(Enquiry $enquiry): Enquiry
    {
        $enquiry->markAsRead();
        return $enquiry;
    }
}


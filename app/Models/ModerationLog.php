<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModerationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'notes',
        'reason',
        'ip_address',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByListing($query, int $listingId)
    {
        return $query->where('listing_id', $listingId);
    }

    public function scopeByAdmin($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }
}


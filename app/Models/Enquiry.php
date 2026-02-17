<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_READ = 'read';
    const STATUS_REPLIED = 'replied';
    const STATUS_SPAM = 'spam';
    const STATUS_BLOCKED = 'blocked';

    protected $fillable = [
        'listing_id',
        'name',
        'email',
        'phone',
        'message',
        'ip_address',
        'user_agent',
        'session_id',
        'status',
        'is_spam',
        'spam_reason',
    ];

    protected $casts = [
        'is_spam' => 'boolean',
        'read_at' => 'datetime',
        'replied_at' => 'datetime',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isRead(): bool
    {
        return $this->status === self::STATUS_READ;
    }

    public function isSpam(): bool
    {
        return $this->is_spam || $this->status === self::STATUS_SPAM;
    }

    public function markAsRead(): void
    {
        if ($this->isPending()) {
            $this->update([
                'status' => self::STATUS_READ,
                'read_at' => now(),
            ]);
        }
    }

    public function markAsSpam(string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_SPAM,
            'is_spam' => true,
            'spam_reason' => $reason,
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeNotSpam($query)
    {
        return $query->where('is_spam', false);
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
}


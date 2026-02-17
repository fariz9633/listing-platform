<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_MODERATION = 'pending_moderation';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_SUSPENDED = 'suspended';

    const PRICING_HOURLY = 'hourly';
    const PRICING_FIXED = 'fixed';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'slug',
        'category',
        'city',
        'suburb',
        'pricing_type',
        'price',
        'price_min',
        'price_max',
        'status',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_min' => 'decimal:2',
        'price_max' => 'decimal:2',
        'view_count' => 'integer',
        'enquiry_count' => 'integer',
        'submitted_at' => 'datetime',
        'moderated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($listing) {
            if (empty($listing->slug)) {
                $listing->slug = Str::slug($listing->title) . '-' . Str::random(6);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enquiries()
    {
        return $this->hasMany(Enquiry::class);
    }

    public function moderationLogs()
    {
        return $this->hasMany(ModerationLog::class);
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPendingModeration(): bool
    {
        return $this->status === self::STATUS_PENDING_MODERATION;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    public function canTransitionTo(string $newStatus): bool
    {
        $validTransitions = [
            self::STATUS_DRAFT => [self::STATUS_PENDING_MODERATION],
            self::STATUS_PENDING_MODERATION => [self::STATUS_APPROVED, self::STATUS_REJECTED],
            self::STATUS_APPROVED => [self::STATUS_SUSPENDED, self::STATUS_PENDING_MODERATION],
            self::STATUS_REJECTED => [self::STATUS_PENDING_MODERATION],
            self::STATUS_SUSPENDED => [self::STATUS_PENDING_MODERATION, self::STATUS_APPROVED],
        ];

        return in_array($newStatus, $validTransitions[$this->status] ?? []);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePendingModeration($query)
    {
        return $query->where('status', self::STATUS_PENDING_MODERATION);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByLocation($query, ?string $city = null, ?string $suburb = null)
    {
        if ($city) {
            $query->where('city', $city);
        }
        if ($suburb) {
            $query->where('suburb', $suburb);
        }
        return $query;
    }

    public function scopeByPriceRange($query, ?float $minPrice = null, ?float $maxPrice = null)
    {
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }
        return $query;
    }

    public function scopeSearch($query, ?string $keyword = null)
    {
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'LIKE', "%{$keyword}%")
                  ->orWhere('description', 'LIKE', "%{$keyword}%");
            });
        }
        return $query;
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->pricing_type === self::PRICING_HOURLY && $this->price_min && $this->price_max) {
            return '$' . number_format($this->price_min, 2) . ' - $' . number_format($this->price_max, 2) . '/hr';
        }

        if ($this->price) {
            $suffix = $this->pricing_type === self::PRICING_HOURLY ? '/hr' : '';
            return '$' . number_format($this->price, 2) . $suffix;
        }

        return 'Contact for pricing';
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}


<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    const ROLE_GUEST = 'guest';
    const ROLE_PROVIDER = 'provider';
    const ROLE_ADMIN = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function moderationLogs()
    {
        return $this->hasMany(ModerationLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isProvider(): bool
    {
        return $this->role === self::ROLE_PROVIDER;
    }

    public function isGuest(): bool
    {
        return $this->role === self::ROLE_GUEST;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeProviders($query)
    {
        return $query->where('role', self::ROLE_PROVIDER);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }
}

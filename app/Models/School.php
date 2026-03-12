<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $fillable = [
        'name', 'slug', 'logo', 'address', 'phone', 'email', 'motto',
        'status', 'activated_at', 'banned_at', 'ban_reason',
        'activated_by', 'banned_by','subscription_expires_at', 'subscription_status',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'banned_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
    ];

    // ── Status Helpers ────────────────────────────────────────────
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isBanned(): bool
    {
        return $this->status === 'banned';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active' => '<span class="badge bg-success">Active</span>',
            'banned' => '<span class="badge bg-danger">Banned</span>',
            default => '<span class="badge bg-warning text-dark">Pending</span>',
        };
    }

    // ── Relationships ─────────────────────────────────────────────
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'school_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(AcademicSession::class);
    }

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class);
    }

    public function feeTypes(): HasMany
    {
        return $this->hasMany(FeeType::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function proprietor()
    {
        return $this->users()->where('role', 'proprietor')->first();
    }

    public function staffCount(): int
    {
        return $this->users()->whereIn('role', ['admin', 'staff', 'school-user'])->count();
    }

    public function studentCount(): int
    {
        return $this->students()->where('is_active', true)->count();
    }

    public function subscriptions()
{
    return $this->hasMany(\App\Models\Subscription::class);
}

public function latestSubscription()
{
    return $this->hasOne(\App\Models\Subscription::class)->latestOfMany();
}

public function isSubscriptionActive(): bool
{
    return $this->subscription_expires_at
        && now()->isBefore($this->subscription_expires_at);
}

public function subscriptionDaysRemaining(): int
{
    if (!$this->subscription_expires_at) return 0;
    return max(0, (int) now()->diffInDays($this->subscription_expires_at, false));
}
}

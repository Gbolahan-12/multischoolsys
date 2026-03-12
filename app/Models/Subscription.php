<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'school_id', 'recorded_by', 'amount', 'payment_date',
        'payment_method', 'reference', 'duration_months',
        'starts_at', 'expires_at', 'note',
    ];

    protected $casts = [
        'starts_at'    => 'datetime',
        'expires_at'   => 'datetime',
        'payment_date' => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ── Helpers ───────────────────────────────────────────────
    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    public function daysRemaining(): int
    {
        return max(0, (int) now()->diffInDays($this->expires_at, false));
    }

    public function daysOverdue(): int
    {
        return max(0, (int) $this->expires_at->diffInDays(now()));
    }

    public function getDurationLabelAttribute(): string
    {
        return match($this->duration_months) {
            1  => '1 Month',
            3  => '3 Months',
            6  => '6 Months',
            12 => '1 Year',
            default => $this->duration_months . ' Months',
        };
    }
}
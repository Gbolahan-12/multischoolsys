<?php

namespace App\Services;

use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    const GRACE_PERIOD_DAYS = 3;

    /**
     * Record a new subscription payment for a school.
     * Extends existing subscription if school is still active.
     */
    public function record(School $school, array $data, User $recordedBy): Subscription
    {
        return DB::transaction(function () use ($school, $data, $recordedBy) {

            $startsAt = $school->subscription_expires_at && $school->subscription_expires_at->isFuture()
                ? $school->subscription_expires_at
                : now();

            $expiresAt = $startsAt->copy()->addMonths((int)$data['duration_months']);

            $subscription = Subscription::create([
                'school_id'       => $school->id,
                'recorded_by'     => $recordedBy->id,
                'amount'          => $data['amount'],
                'payment_date'    => $data['payment_date'],
                'payment_method'  => $data['payment_method'],
                'reference'       => $data['reference'] ?? null,
                'duration_months' => (int) $data['duration_months'],
                'starts_at'       => $startsAt,
                'expires_at'      => $expiresAt,
                'note'            => $data['note'] ?? null,
            ]);

            $school->update([
                'status'                  => 'active',
                'subscription_expires_at' => $expiresAt,
                'subscription_status'     => 'active',
                'banned_at'               => null,
                'ban_reason'              => null,
                'banned_by'               => null,
                'activated_at'            => $school->activated_at ?? now(),
                'activated_by'            => $school->activated_by ?? $recordedBy->id,
            ]);

            return $subscription;
        });
    }

    /**
     * Run daily — check all active schools for expired subscriptions.
     * Called by the scheduled command.
     */
    public function checkExpiredSubscriptions(): array
    {
        $warned  = 0;
        $banned  = 0;

        // Schools with subscriptions that have expired
        $schools = School::where('status', 'active')
            ->orWhere('subscription_status', 'warning')
            ->whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '<', now())
            ->get();

        foreach ($schools as $school) {
            $daysOverdue = (int) $school->subscription_expires_at->diffInDays(now());

            if ($daysOverdue > self::GRACE_PERIOD_DAYS) {
                // Grace period over — ban the school
                $school->update([
                    'status'               => 'banned',
                    'banned_at'            => now(),
                    'ban_reason'           => 'Subscription expired. Please renew to regain access.',
                    'subscription_status'  => 'expired',
                ]);
                $banned++;
            } else {
                // Within grace period — set warning
                $school->update([
                    'subscription_status' => 'warning',
                ]);
                $warned++;
            }
        }

        return compact('warned', 'banned');
    }
}
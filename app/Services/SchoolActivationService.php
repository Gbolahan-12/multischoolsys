<?php

namespace App\Services;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SchoolActivationService
{
    public function activate(School $school, User $activatedBy): void
    {
        $school->update([
            'status'       => 'active',
            'activated_at' => now(),
            'activated_by' => $activatedBy->id,
            'banned_at'    => null,
            'ban_reason'   => null,
            'banned_by'    => null,
        ]);
    }

    public function ban(School $school, User $bannedBy, string $reason = ''): void
    {
        $school->update([
            'status'     => 'banned',
            'banned_at'  => now(),
            'banned_by'  => $bannedBy->id,
            'ban_reason' => $reason,
        ]);
    }

    public function reactivate(School $school, User $activatedBy): void
    {
        $school->update([
            'status'       => 'active',
            'activated_at' => now(),
            'activated_by' => $activatedBy->id,
            'banned_at'    => null,
            'ban_reason'   => null,
            'banned_by'    => null,
        ]);
    }
}
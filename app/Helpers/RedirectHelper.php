<?php

namespace App\Helpers;

use App\Models\User;

class RedirectHelper
{
    public static function byRole(User $user)
    {
        return match($user->role) {
            'super-admin' => redirect()->route('superadmin.dashboard'),
            'proprietor'  => redirect()->route('proprietor.dashboard'),
            'admin'       => redirect()->route('admin.dashboard'),
            'staff'       => redirect()->route('staff.dashboard'),
            default       => redirect()->route('login'),
        };
    }


    // public static function byRole(User $user)
    // {
    //     if ($user->hasRole('SuperAdmin')) {
    //         return redirect()->route('superadmin.dashboard');
    //     }

    //     if ($user->hasRole('SchoolOwner')) {
    //         return redirect()->route('proprietor.dashboard');
    //     }

    //     if ($user->hasRole('SchoolAdmin')) {
    //         return redirect()->route('admin-dashboard');
    //     }

    //     return redirect()->route('loginview');
    // }
}

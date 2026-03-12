<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    //
    public function index()
    {
        $stats = [
            'total_schools'   => School::count(),
            'active_schools'  => School::where('status', 'active')->count(),
            'pending_schools' => School::where('status', 'pending')->count(),
            'banned_schools'  => School::where('status', 'banned')->count(),
            'total_students'  => Student::count(),
            'total_staff'     => User::whereIn('role', ['admin', 'staff', 'school-user'])->count(),
        ];

        $recentSchools = School::with(['users' => fn($q) => $q->where('role', 'proprietor')])
            ->latest()
            ->limit(5)
            ->get();

        $pendingSchools = School::with(['users' => fn($q) => $q->where('role', 'proprietor')])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('dashboards.super-admin.dashboard', compact('stats', 'recentSchools', 'pendingSchools'));
    }
}

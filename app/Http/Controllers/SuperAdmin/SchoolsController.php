<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Services\SchoolActivationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolsController extends Controller
{
    public function __construct(private SchoolActivationService $activationService) {}

    public function index(Request $request)
    {
        $schools = School::query()
            ->with(['users' => fn($q) => $q->where('role', 'proprietor')])
            ->withCount(['students', 'users'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dashboards.super-admin.school.index', compact('schools'));
    }

    public function show(School $school)
    {
        $school->load(['users' => fn($q) => $q->where('role', 'proprietor')]);
        $school->loadCount(['students', 'users']);

        $staffCount   = $school->users()->whereIn('role', ['admin', 'staff', 'school-user'])->count();
        $adminCount   = $school->users()->where('role', 'admin')->count();

        return view('dashboards.super-admin.school.show', compact('school', 'staffCount', 'adminCount'));
    }

    public function activate(School $school)
    {
        if ($school->isActive()) {
            return back()->with('info', 'School is already active.');
        }

        $this->activationService->activate($school, Auth::user());

        return back()->with('success', "✅ {$school->name} has been activated. Default roles and permissions have been created.");
    }

    public function ban(Request $request, School $school)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        if ($school->isBanned()) {
            return back()->with('info', 'School is already banned.');
        }

        $this->activationService->ban($school, Auth::user(), $request->reason);

        return back()->with('success', "🚫 {$school->name} has been banned.");
    }

    public function reactivate(School $school)
    {
        if (!$school->isBanned()) {
            return back()->with('info', 'School is not currently banned.');
        }

        $this->activationService->reactivate($school, Auth::user());

        return back()->with('success', "✅ {$school->name} has been reactivated.");
    }
}
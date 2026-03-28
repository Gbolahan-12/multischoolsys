<?php

namespace App\Http\Controllers\Proprietor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * List all admins and staff for this school.
     */
    public function proprietorDashboard()
    {
        $proprietor = Auth::user();

        return view('dashboards.proprietor.dashboard');

    }

    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $users = User::where('school_id', $schoolId)->with('profile')
            ->whereIn('role', ['admin', 'staff'])
            ->latest()
            ->paginate(20);

        return view('dashboards.proprietor.users.index', compact('users'));
    }

    /**
     * Show the form to create a new admin or staff.
     */
    public function create()
    {
        return view('dashboards.proprietor.users.create');
    }

    /**
     * Store a new admin or staff account.
     */
    public function store(Request $request)
    {
        // dd(User::generateStaffId(Auth::user()->school_id));
        // dd($request->all());
        $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users')->where('school_id', Auth::user()->school_id),
            ],
            'role' => ['required', Rule::in(['admin', 'staff'])],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:500'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'employment_date' => ['nullable', 'date'],
        ]);

        $tempPassword = Str::random(10);

        $user = DB::transaction(function () use ($request) {

            $user = User::create([
                'school_id' => Auth::user()->school_id,
                'staff_id' => User::generateStaffId(Auth::user()->school_id),
                'fullname' => $request->fullname,
                'email' => $request->email,
                'password' => Hash::make('password'),
                'role' => $request->role,
                'is_active' => true,
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'qualification' => $request->qualification,
                'employment_date' => $request->employment_date,
            ]);

            return $user;
        });

        // Flash the temp password once — it won't be retrievable again
        return redirect()->route('proprietor.users.index')
            ->with('success', "Account created for {$user->fullname}.")
            ->with('temp_password', $tempPassword)
            ->with('staff_id', $user->staff_id)
            ->with('temp_email', $user->email);
    }

    public function show(User $user)
    {
        $this->authorizeSchoolAccess($user);
        $user->load('profile');

        return view('dashboards.proprietor.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorizeSchoolAccess($user);
        $user->load('profile');

        return view('dashboards.proprietor.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeSchoolAccess($user);

        $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users')->where('school_id', Auth::user()->school_id)->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['admin', 'staff'])],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:500'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'employment_date' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'name' => $request->fullname,
                'email' => $request->email,
                'role' => $request->role,
            ]);

            $user->profile->update([
                'phone' => $request->phone,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'qualification' => $request->qualification,
                'employment_date' => $request->employment_date,
            ]);
        });

        return redirect()->route('proprietor.users.index')
            ->with('success', "{$user->fullname}'s account has been updated.");
    }

    /**
     * Reset a user's password and show them a new temporary one.
     */
    public function resetPassword(User $user)
    {
        $this->authorizeSchoolAccess($user);

        $tempPassword = Str::random(10);
        $user->update(['password' => Hash::make($tempPassword)]);

        return redirect()->route('proprietor.users.show', $user)
            ->with('success', "Password reset for {$user->name}.")
            ->with('temp_password', $tempPassword)
            ->with('temp_email', $user->email);
    }

    /**
     * Ban a user (admin only can be banned by proprietor).
     */
    public function ban(User $user)
    {
        $this->authorizeSchoolAccess($user);

        $user->update([
            'is_active' => false,
            'banned_at' => now(),
            'banned_by' => Auth::id(),
        ]);

        return back()->with('success', "{$user->fullname} has been banned.");
    }

    /**
     * Unban a user.
     */
    public function unban(User $user)
    {
        $this->authorizeSchoolAccess($user);

        $user->update([
            'is_active' => true,
            'banned_at' => null,
            'banned_by' => null,
        ]);

        return back()->with('success', "{$user->fullname} has been unbanned.");
    }

    /**
     * Delete a user permanently.
     */
    public function destroy(User $user)
    {
        $this->authorizeSchoolAccess($user);
        $user->delete();

        return redirect()->route('proprietor.users.index')
            ->with('success', 'Account deleted successfully.');
    }

    /**
     * Ensure the user being accessed belongs to the logged-in proprietor's school.
     * Prevents a proprietor from accessing another school's users via URL manipulation.
     */
    private function authorizeSchoolAccess(User $user): void
    {
        abort_if($user->school_id !== Auth::user()->school_id, 403, 'Unauthorized.');
    }
}

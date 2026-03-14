<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Payment;
use App\Models\Result;
use App\Models\Student;
use App\Models\Term;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminDashboardController extends Controller
{
    //
    public function index()
    {
        $currentSession = AcademicSession::current()->first();
        $currentTerm = Term::current()->first();

        $totalStudents = Student::active()->count();

        // User model uses banned_at column not is_banned
        $totalStaff = User::where('role', 'staff')
            ->whereNull('banned_at')
            ->count();

        // Payments join through fee to get term
        $feesCollected = Payment::when($currentTerm, fn ($q) => $q->whereHas('fee', fn ($q) => $q->where('term_id', $currentTerm->id))
        )->sum('amount_paid');

        // $studentsOwing = Payment::when($currentTerm, fn($q) =>
        //     $q->whereHas('fee', fn($q) => $q->where('term_id', $currentTerm->id))
        // )
        // ->whereIn('status', ['owing', 'partial'])
        // ->distinct('student_id')
        // ->count('student_id');
        $schoolId = Auth::user()->school_id;
        $studentsOwing = Payment::where('school_id', $schoolId)
            ->whereIn('status', ['owing', 'partial'])
            ->when($currentTerm, fn ($q) => $q->whereIn('fee_id', function ($sub) use ($currentTerm) {
                $sub->select('fees.id')
                    ->from('fees')
                    ->join('fee_types', 'fees.fee_type_id', '=', 'fee_types.id')
                    ->where('fees.term_id', $currentTerm->id)
                    ->where('fee_types.type', 'compulsory');
            }))
            ->distinct('student_id')
            ->count('student_id');

        $resultsUploaded = Result::when($currentTerm, fn ($q) => $q->where('term_id', $currentTerm->id)
        )->count();

        // ── Recent Payments ───────────────────────────────────
        $recentPayments = Payment::with(['student', 'fee.feeType'])
            ->when($currentTerm, fn ($q) => $q->whereHas('fee', fn ($q) => $q->where('term_id', $currentTerm->id))
            )
            ->latest('payment_date')
            ->limit(8)
            ->get();

        // ── Chart 1: Fee collection by month (last 6 months) ─
        $feeByMonth = Payment::selectRaw("DATE_FORMAT(payment_date, '%b %Y') as month, DATE_FORMAT(payment_date, '%Y-%m') as sort_key, SUM(amount_paid) as total")
            ->where('payment_date', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month', 'sort_key')
            ->orderBy('sort_key')
            ->get();

        $feeChartLabels = $feeByMonth->pluck('month');
        $feeChartData = $feeByMonth->pluck('total');

        // ── Chart 2: Payment status donut ─────────────────────
        $paymentStatus = Payment::when($currentTerm, fn ($q) => $q->whereHas('fee', fn ($q) => $q->where('term_id', $currentTerm->id))
        )
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $statusPaid = $paymentStatus['paid'] ?? 0;
        $statusPartial = $paymentStatus['partial'] ?? 0;
        $statusOwing = $paymentStatus['owing'] ?? 0;

        // ── Chart 3: Student enrollment growth (last 6 months)
        $enrollmentByMonth = Student::selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, DATE_FORMAT(created_at, '%Y-%m') as sort_key, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month', 'sort_key')
            ->orderBy('sort_key')
            ->get();

        $enrollmentLabels = $enrollmentByMonth->pluck('month');
        $enrollmentData = $enrollmentByMonth->pluck('total');

        // ── Chart 4: Grade distribution ───────────────────────
        $gradeDistribution = Result::when($currentTerm, fn ($q) => $q->where('term_id', $currentTerm->id)
        )
            ->selectRaw('grade, COUNT(*) as count')
            ->whereNotNull('grade')
            ->groupBy('grade')
            ->pluck('count', 'grade');

        $grades = ['A', 'B', 'C', 'D', 'E', 'F'];
        $gradeCounts = collect($grades)->map(fn ($g) => $gradeDistribution[$g] ?? 0);

        return view('dashboards.admin.index', compact(
            'currentSession', 'currentTerm',
            'totalStudents', 'totalStaff', 'feesCollected',
            'studentsOwing', 'resultsUploaded', 'recentPayments',
            'feeChartLabels', 'feeChartData',
            'statusPaid', 'statusPartial', 'statusOwing',
            'enrollmentLabels', 'enrollmentData',
            'grades', 'gradeCounts'
        ));
    }

    public function staffList()
    {
        $users = User::with('profile')
            ->whereIn('role', ['admin', 'staff'])
            ->latest()
            ->paginate(20);

        return view('dashboards.admin.users.index', compact('users'));
    }

    /**
     * Show the form to create a new admin or staff.
     */
    public function staffCreate()
    {
        return view('dashboards.admin.users.create');
    }

    /**
     * Store a new admin or staff account.
     */
    public function staffStore(Request $request)
    {
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
        return redirect()->route('admin.users.index')
            ->with('success', "Account created for {$user->fullname}.")
            ->with('temp_password', $tempPassword)
            ->with('staff_id', $user->staff_id)
            ->with('temp_email', $user->email);
    }

    public function show(User $user)
    {
        $this->authorizeSchoolAccess($user);
        $user->load('profile');

        return view('dashboards.admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorizeSchoolAccess($user);
        $user->load('profile');

        return view('dashboards.admin.users.edit', compact('user'));
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

        return redirect()->route('admin.users.index')
            ->with('success', "{$user->fullname}'s account has been updated.");
    }

    private function authorizeSchoolAccess(User $user): void
    {
        abort_if($user->school_id !== Auth::user()->school_id, 403, 'Unauthorized.');
    }
}

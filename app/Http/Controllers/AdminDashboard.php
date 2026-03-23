<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Controller
{
    //
    public function index()
    {
        $users = User::all();
        // ✅ Total Students
        $totalStudents = User::where('role', 'student')->count();

        // ✅ Current Month
        $now = Carbon::now();

        $currentMonthStudents = User::where('role', 'student')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        // ✅ Last Month
        $lastMonth = Carbon::now()->subMonth();

        $lastMonthStudents = User::where('role', 'student')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        // ✅ Growth Percentage
        if ($lastMonthStudents > 0) {
            $growthPercentage =
                (($currentMonthStudents - $lastMonthStudents) / $lastMonthStudents) * 100;
        } else {
            $growthPercentage = $currentMonthStudents > 0 ? 100 : 0;
        }

        $growthPercentage = round($growthPercentage, 2);

        // ✅ Monthly Chart Data (Last 12 Months)
        $monthlyRaw = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('role', 'student')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Create 12 months default = 0
        $monthlyGrowth = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyGrowth[] = $monthlyRaw[$i] ?? 0;
        }
        $totalRevenue = Payment::sum('amount_paid');

        // ✅ Current Month Revenue
        $now = Carbon::now();

        $currentMonthRevenue = Payment::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('amount_paid');

        // ✅ Last Month Revenue
        $lastMonth = Carbon::now()->subMonth();

        $lastMonthRevenue = Payment::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->sum('amount_paid');

        // ✅ Revenue Growth %
        if ($lastMonthRevenue > 0) {
            $revenueGrowth = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        } else {
            $revenueGrowth = $currentMonthRevenue > 0 ? 100 : 0;
        }

        $revenueGrowth = round($revenueGrowth, 2);

        // ✅ Monthly Revenue For Chart (Jan–Dec)
        $monthlyRevenueRaw = Payment::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(amount_paid) as total')
        )
            ->whereYear('created_at', $now->year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyRevenue = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyRevenue[] = (float) ($monthlyRevenueRaw[$i] ?? 0);
        }

        return view('dashboards.admin.index', compact(
            'totalStudents',
            'currentMonthStudents',
            'lastMonthStudents',
            'growthPercentage',
            'monthlyGrowth',
            'totalRevenue',
            'currentMonthRevenue',
            'lastMonthRevenue',
            'revenueGrowth',
            'monthlyRevenue'
        ));
    }

    public function indexDashboard()
    {
        // ✅ Total Students
        $totalStudents = User::where('role', 'student')->count();

        // ✅ Current Month
        $now = Carbon::now();

        $currentMonthStudents = User::where('role', 'student')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        // ✅ Last Month
        $lastMonth = Carbon::now()->subMonth();

        $lastMonthStudents = User::where('role', 'student')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        // ✅ Growth Percentage
        if ($lastMonthStudents > 0) {
            $growthPercentage =
                (($currentMonthStudents - $lastMonthStudents) / $lastMonthStudents) * 100;
        } else {
            $growthPercentage = $currentMonthStudents > 0 ? 100 : 0;
        }

        $growthPercentage = round($growthPercentage, 2);

        // ✅ Monthly Chart Data (Last 12 Months)
        $monthlyGrowth = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as total')
        )
            ->where('role', 'student')
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('dashboards.admin.index', compact(
            'totalStudents',
            'currentMonthStudents',
            'lastMonthStudents',
            'growthPercentage',
            'monthlyGrowth'
        ));
    }

    public function createPaymentForm()
    {
        return view('dashboards.admin.payment.makepayment');
    }

    public function completePaymentList()
    {
        return view('dashboards.admin.payment.complete');
    }

    // public function defaulters(Request $request)
    // {
    //     $schoolId = Auth::user()->school_id;
    //     $currentSession = AcademicSession::current()->first();
    //     $currentTerm = Term::current()->first();

    //     // ── Filter options ────────────────────────────────────────
    //     $sessions = AcademicSession::where('school_id', $schoolId)
    //         ->orderByDesc('created_at')->get();
    //     $selectedSessionId = $request->session_id ?? $currentSession?->id;

    //     $terms = collect();
    //     if ($selectedSessionId) {
    //         $terms = Term::where('school_id', $schoolId)
    //             ->where('session_id', $selectedSessionId)
    //             ->orderByRaw("FIELD(name, 'first', 'second', 'third')")
    //             ->get();
    //     }

    //     $selectedTermId = $request->term_id ?? $currentTerm?->id;

    //     // ── Optional fees for selected term ───────────────────────
    //     $fees = collect();
    //     if ($selectedTermId) {
    //         $fees = Fee::with('feeType')
    //             ->where('school_id', $schoolId)
    //             ->where('term_id', $selectedTermId)
    //             ->whereHas('feeType', fn ($q) => $q->where('type', 'optional'))
    //             ->get();
    //     }

    //     $selectedFeeId = $request->fee_id;

    //     // ── Payments query ────────────────────────────────────────
    //     $payments = collect();
    //     $summary = null;

    //     if ($selectedTermId) {
    //         $query = Payment::with(['student', 'fee.feeType', 'fee.schoolClass', 'receivedBy'])
    //             ->where('school_id', $schoolId)
    //             ->whereHas('fee', fn ($q) => $q
    //                 ->where('term_id', $selectedTermId)
    //                 ->whereHas('feeType', fn ($fq) => $fq->where('type', 'optional'))
    //             )
    //             ->when($selectedFeeId, fn ($q) => $q->where('fee_id', $selectedFeeId))
    //             ->when($request->status, fn ($q) => $q->where('status', $request->status))
    //             ->when($request->search, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('first_name', 'like', "%{$request->search}%")
    //                 ->orWhere('last_name', 'like', "%{$request->search}%")
    //                 ->orWhere('admission_number', 'like', "%{$request->search}%")
    //             ))
    //             ->orderByDesc('payment_date');

    //         $all = (clone $query)->get();
    //         $summary = [
    //             'total' => $all->count(),
    //             'paid' => $all->where('status', 'paid')->count(),
    //             'partial' => $all->where('status', 'partial')->count(),
    //             'owing' => $all->where('status', 'owing')->count(),
    //             'total_amount' => $all->sum('amount_paid'),
    //         ];

    //         $payments = $query->paginate(20)->withQueryString();
    //     }

    //     return view('dashboards.admin.payment.defaulters', compact(
    //         'sessions', 'terms', 'fees', 'payments', 'summary',
    //         'selectedSessionId', 'selectedTermId', 'selectedFeeId',
    //     ));
    // }

    public function storeStudent(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'nullable|email',
            'school_id' => 'required|exists:schools,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'fullname' => $validated['fullname'],
                'email' => $validated['email'],
                'school_id' => $validated['school_id'],
                'class_id' => $validated['class_id'],
                'password' => bcrypt('password'),
                'role' => 'student',
            ]);
            $admissionNo = Student::generateAdmissionNo($validated['school_id']);
            Student::create([
                'user_id' => $user->id,
                'school_id' => $validated['school_id'],
                'class_id' => $validated['class_id'],
                'admission_no' => $admissionNo,
            ]);

        });

        return back()->with('success', 'Student created successfully');
    }

    public function storePayment(Request $request)
    {
        dd($request->all());
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'amount_paid' => 'required|numeric|min:0',
            'fee_id' => 'required|string|max:50',
            'payment_date' => 'required|date',
        ]);

        $validated['paid_by'] = Auth::id();

        DB::table('payments')->insert($validated);

        return back()->with('success', 'Payment recorded successfully');
    }

    public function create()
    {
        $classes = SchoolClass::all();
        $schools = School::all();

        return view('dashboards.admin.class.assignclass', compact('classes', 'schools'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'school_id' => 'required|exists:schools,id',
            'classes' => 'required|array',
            'classes.*' => 'exists:classes,id',
        ]);

        $subject = Subject::create([
            'name' => $data['name'],
            'school_id' => $data['school_id'],
        ]);

        $subject->classes()->sync($data['classes']);

        return redirect()->route('subjects.create')
            ->with('success', 'Subject created and assigned successfully.');
    }
}

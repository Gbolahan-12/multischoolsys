<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
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

    public function defaulters()
    {
        $defaultersPayments = Payment::with(['student', 'fee'])
            ->whereHas('fee', function ($query) {
                $query->where('type', 'defaulter');
            })
            ->get();

        return view('dashboards.admin.payment.defaulters', compact('defaultersPayments'));
    }

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

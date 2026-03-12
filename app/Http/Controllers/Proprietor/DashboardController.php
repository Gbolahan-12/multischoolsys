<?php

namespace App\Http\Controllers\Proprietor;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Term;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $schoolId      = Auth::user()->school_id;
        // $currentSession = AcademicSession::current()->with('currentTerm')->first();
        $currentSession = AcademicSession::current()->first();
        $currentTerm    = Term::current()->first();

        // ── Snapshot Stats ──────────────────────────────────────
        $totalStudents  = Student::where('school_id', $schoolId)->where('is_active', true)->count();
        $totalStaff     = User::where('school_id', $schoolId)->where('role', 'staff')->where('is_active', true)->count();
        $totalAdmins    = User::where('school_id', $schoolId)->where('role', 'admin')->where('is_active', true)->count();

        // Payments for current term
        $termPayments   = Payment::where('school_id', $schoolId)
            ->when($currentTerm, fn($q) => $q->whereIn('fee_id', function($sub) use ($currentTerm) {
                $sub->select('id')->from('fees')->where('term_id', $currentTerm->id);
            }))
            ->selectRaw('SUM(amount_paid) as collected, SUM(balance) as outstanding')
            ->first();

        $collected    = $termPayments->collected ?? 0;
        $outstanding  = $termPayments->outstanding ?? 0;

        // Students owing in current term
        $studentsOwing = Payment::where('school_id', $schoolId)
            ->whereIn('status', ['owing', 'partial'])
            ->when($currentTerm, fn($q) => $q->whereIn('fee_id', function($sub) use ($currentTerm) {
                $sub->select('id')->from('fees')->where('term_id', $currentTerm->id);
            }))
            ->distinct('student_id')
            ->count('student_id');

        // ── Payment Growth (last 6 months) ──────────────────────
        $paymentGrowth = Payment::where('school_id', $schoolId)
            ->where('payment_date', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(payment_date, '%b %Y') as month, MONTH(payment_date) as month_num, YEAR(payment_date) as year, SUM(amount_paid) as total")
            ->groupByRaw("DATE_FORMAT(payment_date, '%b %Y'), MONTH(payment_date), YEAR(payment_date)")
            ->orderByRaw('year ASC, month_num ASC')
            ->get();

        // ── Student Growth (last 6 months by admission) ─────────
        $studentGrowth = Student::where('school_id', $schoolId)
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, MONTH(created_at) as month_num, YEAR(created_at) as year, COUNT(*) as total")
            ->groupByRaw("DATE_FORMAT(created_at, '%b %Y'), MONTH(created_at), YEAR(created_at)")
            ->orderByRaw('year ASC, month_num ASC')
            ->get();

        // ── Payment Status Breakdown (current term) ─────────────
        $paymentBreakdown = Payment::where('school_id', $schoolId)
            ->when($currentTerm, fn($q) => $q->whereIn('fee_id', function($sub) use ($currentTerm) {
                $sub->select('id')->from('fees')->where('term_id', $currentTerm->id);
            }))
            ->selectRaw("status, COUNT(DISTINCT student_id) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        // ── Recent Payments ─────────────────────────────────
        $recentPayments = Payment::where('school_id', $schoolId)
            ->with(['student', 'fee.feeType'])
            ->latest('payment_date')
            ->limit(6)
            ->get();

        return view('dashboards.proprietor.dashboard', compact(
            'currentSession', 'currentTerm',
            'totalStudents', 'totalStaff', 'totalAdmins',
            'collected', 'outstanding', 'studentsOwing',
            'paymentGrowth', 'studentGrowth', 'paymentBreakdown',
            'recentPayments'
        ));
    }
}
<?php

namespace App\Http\Controllers\Proprietor;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Term;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $schoolId       = Auth::user()->school_id;
        $currentSession = AcademicSession::current()->first();
        $currentTerm    = Term::current()->first();

        // ── Snapshot Stats ────────────────────────────────────────
        $totalStudents = Student::where('school_id', $schoolId)->where('is_active', true)->count();
        $totalStaff    = User::where('school_id', $schoolId)->whereIn('role', ['admin', 'staff'])->where('is_active', true)->count();
        $totalAdmins   = User::where('school_id', $schoolId)->where('role', 'admin')->where('is_active', true)->count();

        // ── Compulsory fee IDs for current term ───────────────────
        $compulsoryFeeIds = $currentTerm
            ? Fee::where('school_id', $schoolId)
                ->where('term_id', $currentTerm->id)
                ->whereHas('feeType', fn($q) => $q->where('type', 'compulsory'))
                ->pluck('id')
            : collect();

        // ── Payments collected & outstanding (compulsory only) ────
        $termPayments = Payment::where('school_id', $schoolId)
            ->when($compulsoryFeeIds->isNotEmpty(), fn($q) => $q->whereIn('fee_id', $compulsoryFeeIds))
            ->selectRaw('SUM(amount_paid) as collected, SUM(balance) as outstanding')
            ->first();

        $collected   = $termPayments->collected   ?? 0;
        $outstanding = $termPayments->outstanding ?? 0;

        // ── Students owing (compulsory fees only) ─────────────────
        $studentsOwing = 0;

        if ($currentTerm && $compulsoryFeeIds->isNotEmpty()) {

            $studentsThatPaid = Payment::where('school_id', $schoolId)
                ->whereIn('fee_id', $compulsoryFeeIds)
                ->pluck('student_id')
                ->unique();

            $partialStudentIds = Payment::where('school_id', $schoolId)
                ->whereIn('fee_id', $compulsoryFeeIds)
                ->whereIn('status', ['partial', 'owing'])
                ->pluck('student_id')
                ->unique();

            $noPaymentStudentIds = Student::where('school_id', $schoolId)
                ->where('is_active', true)
                ->whereNotIn('id', $studentsThatPaid)
                ->pluck('id');

            $studentsOwing = $partialStudentIds
                ->merge($noPaymentStudentIds)
                ->unique()
                ->count();
        }

        // ── Financial Summary (compulsory fees only) ──────────────
        // Amount Expected = sum of all compulsory fee amounts × number of students
        // We calculate it as: total compulsory fees defined for current term
        // multiplied by applicable students per fee (class-specific or all)
        $amountExpected = 0;
        $amountPaid     = (float) ($collected ?? 0);
        $amountRemaining = 0;
        $defaultersCount = $studentsOwing;

        if ($currentTerm && $compulsoryFeeIds->isNotEmpty()) {
            // Get all compulsory fees for this term
            $compulsoryFees = Fee::where('school_id', $schoolId)
                ->where('term_id', $currentTerm->id)
                ->whereHas('feeType', fn($q) => $q->where('type', 'compulsory'))
                ->get();

            foreach ($compulsoryFees as $fee) {
                if ($fee->class_id) {
                    // Class-specific fee — count students in that class this term
                    $studentCount = Student::where('school_id', $schoolId)
                        ->where('is_active', true)
                        ->whereHas('classAssignments', fn($q) => $q
                            ->where('class_id', $fee->class_id)
                            ->where('term_id', $currentTerm->id)
                        )
                        ->count();
                } else {
                    // All classes fee — count all active students
                    $studentCount = $totalStudents;
                }

                $amountExpected += (float) $fee->amount * $studentCount;
            }

            $amountRemaining = max(0, $amountExpected - $amountPaid);
        }

        // ── Payment Growth (last 6 months) ────────────────────────
        $paymentGrowth = Payment::where('school_id', $schoolId)
            ->where('payment_date', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(payment_date, '%b %Y') as month, MONTH(payment_date) as month_num, YEAR(payment_date) as year, SUM(amount_paid) as total")
            ->groupByRaw("DATE_FORMAT(payment_date, '%b %Y'), MONTH(payment_date), YEAR(payment_date)")
            ->orderByRaw('year ASC, month_num ASC')
            ->get();

        // ── Student Growth (last 6 months by admission) ───────────
        $studentGrowth = Student::where('school_id', $schoolId)
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, MONTH(created_at) as month_num, YEAR(created_at) as year, COUNT(*) as total")
            ->groupByRaw("DATE_FORMAT(created_at, '%b %Y'), MONTH(created_at), YEAR(created_at)")
            ->orderByRaw('year ASC, month_num ASC')
            ->get();

        // ── Payment Status Breakdown (compulsory fees only) ───────
        $paymentBreakdown = Payment::where('school_id', $schoolId)
            ->when($compulsoryFeeIds->isNotEmpty(), fn($q) => $q->whereIn('fee_id', $compulsoryFeeIds))
            ->selectRaw("status, COUNT(DISTINCT student_id) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        $noPaymentCount = $currentTerm && $compulsoryFeeIds->isNotEmpty()
            ? Student::where('school_id', $schoolId)
                ->where('is_active', true)
                ->whereNotIn('id',
                    Payment::where('school_id', $schoolId)
                        ->whereIn('fee_id', $compulsoryFeeIds)
                        ->pluck('student_id')
                )
                ->count()
            : 0;

        if ($noPaymentCount > 0) {
            $paymentBreakdown = $paymentBreakdown->toArray();
            $paymentBreakdown['owing'] = ($paymentBreakdown['owing'] ?? 0) + $noPaymentCount;
            $paymentBreakdown = collect($paymentBreakdown);
        }

        // ── Recent Payments ───────────────────────────────────────
        $recentPayments = Payment::where('school_id', $schoolId)
            ->with(['student', 'fee.feeType'])
            ->latest('payment_date')
            ->limit(6)
            ->get();

        return view('dashboards.proprietor.dashboard', compact(
            'currentSession', 'currentTerm',
            'totalStudents', 'totalStaff', 'totalAdmins',
            'collected', 'outstanding', 'studentsOwing',
            'amountExpected', 'amountPaid', 'amountRemaining', 'defaultersCount',
            'paymentGrowth', 'studentGrowth', 'paymentBreakdown',
            'recentPayments'
        ));
    }
}
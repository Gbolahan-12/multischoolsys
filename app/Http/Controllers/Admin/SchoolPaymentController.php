<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\School;
use App\Models\Student;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolPaymentController extends Controller
{
    //
    public function create()
    {
        $students = User::where('role', 'student')->get();
        $fees = Fee::with('class')->get();

        return view('dashboards.admin.payment.makepayment', compact('students', 'fees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'fee_id' => 'required|exists:fees,id',
            'amount_paid' => 'required|numeric|min:1',
        ]);

        $fee = Fee::findOrFail($data['fee_id']);

        $totalPaid = Payment::where('student_id', $data['student_id'])
            ->where('fee_id', $data['fee_id'])
            ->sum('amount_paid');

        $newTotal = $totalPaid + $data['amount_paid'];
        $remainingBalance = $fee->amount - $totalPaid;

        if ($newTotal > $fee->amount) {
            return back()->withErrors([
                'amount_paid' => "Payment exceeds remaining balance. Remaining balance is {$remainingBalance}.",
            ])->withInput();
        }
        Payment::create([
            'student_id' => $data['student_id'],
            'fee_id' => $data['fee_id'],
            'amount_paid' => $data['amount_paid'],
            'recorded_by' => Auth::id(),
        ]);

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function allPayments()
    {
        $schools = School::all();
        $payments = Payment::with('student', 'fee.class')->get();

        return view('dashboards.admin.payment.myrecordpaymentlist', compact('payments', 'schools'));
    }

    public function completePayments()
    {
        $schools = School::all();

        // Group payments by student + fee
        $completePayments = Payment::with('student', 'fee.class', 'recorder')
            ->get()
            ->groupBy(function ($payment) {
                return $payment->student_id.'-'.$payment->fee_id;
            })
            ->map(function ($group) {
                $totalPaid = $group->sum('amount_paid');
                $feeAmount = $group->first()->fee->amount;

                if ($totalPaid >= $feeAmount) {
                    return [
                        'student' => $group->first()->student,
                        'fee' => $group->first()->fee,
                        'total_paid' => $totalPaid,
                        'fee_amount' => $feeAmount,
                        'payments' => $group,
                    ];
                }

                return null;
            })
            ->filter(); // remove nulls

        return view('dashboards.admin.payment.complete', compact('completePayments', 'schools'));
    }

    public function defaulters(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $currentSession = AcademicSession::current()->first();
        $currentTerm = Term::current()->first();

        // ── Filter options ────────────────────────────────────────
        $sessions = AcademicSession::where('school_id', $schoolId)
            ->orderByDesc('created_at')->get();
        $selectedSessionId = $request->session_id ?? $currentSession?->id;

        $terms = collect();
        if ($selectedSessionId) {
            $terms = Term::where('school_id', $schoolId)
                ->where('session_id', $selectedSessionId)
                ->orderByRaw("FIELD(name, 'first', 'second', 'third')")
                ->get();
        }

        $selectedTermId = $request->term_id ?? $currentTerm?->id;

        // ── Compulsory fee IDs for selected term ──────────────────
        $compulsoryFeeIds = collect();
        if ($selectedTermId) {
            $compulsoryFeeIds = Fee::where('school_id', $schoolId)
                ->where('term_id', $selectedTermId)
                ->whereHas('feeType', fn ($q) => $q->where('type', 'compulsory'))
                ->pluck('id');
        }

        // ── Build defaulters list ─────────────────────────────────
        // Defaulters = students who have NOT fully paid at least one compulsory fee
        $defaulters = collect();
        $summary = null;

        if ($selectedTermId && $compulsoryFeeIds->isNotEmpty()) {

            // Students with partial or owing payments on compulsory fees
            $partialStudentIds = Payment::where('school_id', $schoolId)
                ->whereIn('fee_id', $compulsoryFeeIds)
                ->whereIn('status', ['partial', 'owing'])
                ->pluck('student_id')
                ->unique();

            // Active students with NO payment record at all for compulsory fees
            $paidStudentIds = Payment::where('school_id', $schoolId)
                ->whereIn('fee_id', $compulsoryFeeIds)
                ->pluck('student_id')
                ->unique();

            $noPaymentStudentIds = Student::where('school_id', $schoolId)
                ->where('is_active', true)
                ->whereNotIn('id', $paidStudentIds)
                ->pluck('id');

            // Merge both groups
            $allDefaulterIds = $partialStudentIds->merge($noPaymentStudentIds)->unique();

            // Get student records with their payment info
            $defaulters = Student::where('school_id', $schoolId)
                ->whereIn('id', $allDefaulterIds)
                ->when($request->search, fn ($q) => $q->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('last_name', 'like', "%{$request->search}%")
                    ->orWhere('admission_number', 'like', "%{$request->search}%")
                )
                ->with(['currentAssignment.schoolClass'])
                ->withSum(['payments as amount_paid' => fn ($q) => $q->whereIn('fee_id', $compulsoryFeeIds),
                ], 'amount_paid')
                ->orderBy('last_name')
                ->paginate(20)
                ->withQueryString();

            // Total compulsory fee amount per student (fees applicable to all classes or their class)
            $totalFeeAmount = Fee::where('school_id', $schoolId)
                ->where('term_id', $selectedTermId)
                ->whereHas('feeType', fn ($q) => $q->where('type', 'compulsory'))
                ->sum('amount');

            $summary = [
                'total_defaulters' => $allDefaulterIds->count(),
                'no_payment' => $noPaymentStudentIds->count(),
                'partial' => $partialStudentIds->count(),
                'total_outstanding' => Payment::where('school_id', $schoolId)
                    ->whereIn('fee_id', $compulsoryFeeIds)
                    ->whereIn('status', ['partial', 'owing'])
                    ->sum('balance'),
            ];
        }

        return view('dashboards.admin.payment.defaulters-compulsory', compact(
            'sessions', 'terms', 'defaulters', 'summary',
            'selectedSessionId', 'selectedTermId',
            'compulsoryFeeIds', 'currentTerm', 'currentSession'
        ));
    }

    public function isPaymentComplete() {}
}

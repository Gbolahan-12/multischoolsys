<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $currentSession = AcademicSession::current()->first();
        $currentTerm = Term::current()->first();
        $classes = SchoolClass::with(['level', 'section'])->orderBy('name')->get();

        $payments = Payment::with(['student', 'fee.feeType', 'fee.schoolClass', 'recordedBy'])
            ->when($currentTerm, fn ($q) => $q->whereHas('fee', fn ($q) => $q->where('term_id', $currentTerm->id))
            )
            ->when($request->search, fn ($q) => $q->whereHas('student', fn ($q) => $q->where('first_name', 'like', "%{$request->search}%")
                ->orWhere('last_name', 'like', "%{$request->search}%")
                ->orWhere('admission_number', 'like', "%{$request->search}%")
            )
            )
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->class_id, fn ($q) => $q->whereHas('fee', fn ($q) => $q->where('class_id', $request->class_id))
            )
            ->latest('payment_date')
            ->paginate(20)
            ->withQueryString();

        // Summary
        $summary = [
            'total_collected' => Payment::when($currentTerm, fn ($q) => $q->whereHas('fee', fn ($q) => $q->where('term_id', $currentTerm->id))
            )->sum('amount_paid'),
            'total_outstanding' => Payment::when($currentTerm, fn ($q) => $q->whereHas('fee', fn ($q) => $q->where('term_id', $currentTerm->id))
            )->sum('balance'),
            'paid_count' => Payment::when($currentTerm, fn ($q) => $q->whereHas('fee', fn ($q) => $q->where('term_id', $currentTerm->id))
            )->where('status', 'paid')->count(),
            'owing_count' => Payment::when($currentTerm, fn ($q) => $q->whereHas('fee', fn ($q) => $q->where('term_id', $currentTerm->id))
            )->whereIn('status', ['owing', 'partial'])->count(),
        ];

        return view('dashboards.admin.payment.index', compact(
            'payments', 'classes', 'summary', 'currentSession', 'currentTerm'
        ));
    }

    public function create(Request $request)
    {
        $currentSession = AcademicSession::current()->first();
        $currentTerm = Term::current()->first();

        // All active students for the dropdown
        $students = Student::active()->orderBy('first_name')->get();

        // All fees for current term
        $fees = Fee::with(['feeType', 'schoolClass'])
            ->when($currentTerm, fn ($q) => $q->where('term_id', $currentTerm->id))
            ->orderBy('created_at', 'desc')
            ->get();

        // Pre-select student if passed via query string
        $student = $request->student_id
            ? Student::find($request->student_id)
            : null;

        return view('dashboards.admin.payment.create', compact('fees', 'students', 'student', 'currentSession', 'currentTerm'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'fee_id' => ['required', 'exists:fees,id'],
            'amount_paid' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', Rule::in(['cash', 'transfer', 'pos', 'cheque'])],
            'reference' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $fee = Fee::findOrFail($request->fee_id);
        $student = Student::findOrFail($request->student_id);

        abort_if($fee->school_id !== Auth::user()->school_id, 403);
        abort_if($student->school_id !== Auth::user()->school_id, 403);

        // Check if payment already exists for this student+fee
        $existing = Payment::where('student_id', $student->id)
            ->where('fee_id', $fee->id)
            ->first();

        $previouslyPaid = $existing ? $existing->amount_paid : 0;
        $totalPaid = $previouslyPaid + $request->amount_paid;
        $balance = max(0, $fee->amount - $totalPaid);

        $status = match (true) {
            $totalPaid >= $fee->amount => 'paid',
            $totalPaid > 0 => 'partial',
            default => 'owing',
        };

        if ($existing) {
            // Update existing payment record
            $existing->update([
                'amount_paid' => $totalPaid,
                'balance' => $balance,
                'status' => $status,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'reference' => $request->reference,
                'note' => $request->note,
                'received_by' => Auth::id(),
            ]);
        } else {
            Payment::create([
                'school_id' => Auth::user()->school_id,
                'student_id' => $student->id,
                'fee_id' => $fee->id,
                'amount_paid' => $request->amount_paid,
                'balance' => $balance,
                'status' => $status,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'reference' => $request->reference,
                'note' => $request->note,
                'received_by' => Auth::id(),
            ]);
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment of ₦'.number_format($request->amount_paid, 0)." recorded for {$student->short_name}.");
    }

    public function show(Student $student)
    {
        abort_if($student->school_id !== Auth::user()->school_id, 403);

        $currentTerm = Term::current()->first();

        $payments = Payment::with(['fee.feeType', 'fee.schoolClass', 'recordedBy'])
            ->where('student_id', $student->id)
            ->latest('payment_date')
            ->get();

        // All fees for current term applicable to student's class
        $currentAssignment = $student->currentAssignment;
        $termFees = Fee::with(['feeType'])
            ->when($currentTerm, fn ($q) => $q->where('term_id', $currentTerm->id))
            ->where(fn ($q) => $q->whereNull('class_id')
                ->orWhere('class_id', $currentAssignment?->class_id)
            )
            ->get();

        return view('dashboards.admin.payment.show', compact('student', 'payments', 'termFees', 'currentTerm'));
    }

    public function destroy(Payment $payment)
    {
        abort_if($payment->school_id !== Auth::user()->school_id, 403);
        $payment->delete();

        return back()->with('success', 'Payment record deleted.');
    }

    // AJAX: get student fees status
    public function studentFees(Request $request)
    {
        $student = Student::findOrFail($request->student_id);
        abort_if($student->school_id !== Auth::user()->school_id, 403);

        $currentTerm = Term::current()->first();
        $currentAssignment = $student->currentAssignment;

        $fees = Fee::with(['feeType', 'payments' => fn ($q) => $q->where('student_id', $student->id)])
            ->when($currentTerm, fn ($q) => $q->where('term_id', $currentTerm->id))
            ->where(fn ($q) => $q->whereNull('class_id')
                ->orWhere('class_id', $currentAssignment?->class_id)
            )
            ->get()
            ->map(fn ($fee) => [
                'id' => $fee->id,
                'name' => $fee->feeType->name.($fee->schoolClass ? " ({$fee->schoolClass->full_name})" : ' (All Classes)'),
                'amount' => $fee->amount,
                'amount_paid' => $fee->payments->first()?->amount_paid ?? 0,
                'balance' => $fee->payments->first()?->balance ?? $fee->amount,
                'status' => $fee->payments->first()?->status ?? 'owing',
            ]);

        return response()->json($fees);
    }

    public function indexPaid(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        // ── Load all sessions for the session dropdown ────────
        $sessions = AcademicSession::where('school_id', $schoolId)
            ->orderByDesc('created_at')
            ->get();

        // ── Determine active session ──────────────────────────
        $selectedSessionId = $request->session_id
            ?? AcademicSession::where('school_id', $schoolId)
                ->where('is_current', true)
                ->value('id');

        // ── Load terms for selected session ───────────────────
        $terms = collect();
        if ($selectedSessionId) {
            $terms = Term::where('school_id', $schoolId)
                ->where('session_id', $selectedSessionId)
                ->orderByRaw("FIELD(name, 'first', 'second', 'third')")
                ->get();
        }

        // ── Determine selected term ───────────────────────────
        $selectedTermId = $request->term_id
            ?? Term::where('school_id', $schoolId)
                ->where('is_current', true)
                ->value('id');

        // ── Load fees for selected term ───────────────────────
        $fees = collect();
        if ($selectedTermId) {
            $fees = Fee::with('feeType')
                ->where('school_id', $schoolId)
                ->where('term_id', $selectedTermId)
                ->get();
        }

        // ── Selected fee ──────────────────────────────────────
        $selectedFeeId = $request->fee_id;

        // ── Build payments query ──────────────────────────────
        $payments = collect();
        $summary = null;

        if ($selectedTermId) {
            $query = Payment::with([
                'student',
                'fee.feeType',
                'fee.class',
                'receivedBy',
            ])
                ->where('school_id', $schoolId)
                ->whereHas('fee', fn ($q) => $q->where('term_id', $selectedTermId))
                ->where('status', 'paid') // only fully paid
                ->when($selectedFeeId, fn ($q) => $q->where('fee_id', $selectedFeeId))
                ->when($request->search, fn ($q) => $q->whereHas('student', function ($q) use ($request) {
                    $q->where('first_name', 'like', "%{$request->search}%")
                        ->orWhere('last_name', 'like', "%{$request->search}%")
                        ->orWhere('admission_number', 'like', "%{$request->search}%");
                }))
                ->orderByDesc('payment_date');

            // Summary stats before paginating
            $allPayments = (clone $query)->get();
            $summary = [
                'total_paid' => $allPayments->count(),
                'total_amount' => $allPayments->sum('amount_paid'),
                'cash' => $allPayments->where('payment_method', 'cash')->count(),
                'transfer' => $allPayments->where('payment_method', 'transfer')->count(),
                'pos' => $allPayments->where('payment_method', 'pos')->count(),
            ];

            $payments = $query->paginate(20)->withQueryString();
        }

        return view('dashboards.admin.payment.paid-students', compact(
            'sessions',
            'terms',
            'fees',
            'payments',
            'summary',
            'selectedSessionId',
            'selectedTermId',
            'selectedFeeId',
        ));
    }

    /**
     * AJAX — load terms when session changes
     */
    public function termsBySession(Request $request)
    {
        $terms = Term::where('school_id', Auth::user()->school_id)
            ->where('session_id', $request->session_id)
            ->orderByRaw("FIELD(name, 'first', 'second', 'third')")
            ->get(['id', 'name']);

        return response()->json($terms);
    }

    /**
     * AJAX — load fees when term changes
     */
    public function feesByTerm(Request $request)
    {
        $fees = Fee::with('feeType')
            ->where('school_id', Auth::user()->school_id)
            ->where('term_id', $request->term_id)
            ->get()
            ->map(fn ($fee) => [
                'id' => $fee->id,
                'name' => $fee->feeType->name.' — ₦'.number_format($fee->amount, 2)
                          .($fee->class ? ' ('.$fee->class->name.')' : ' (All Classes)'),
            ]);

        return response()->json($fees);
    }

    public function indexDefaulter(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $currentTerm = Term::current()->first();
        $currentSession = AcademicSession::current()->first();

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

        // Only defaulter fees for selected term
        $fees = collect();
        if ($selectedTermId) {
            $fees = Fee::with('feeType')
                ->where('school_id', $schoolId)
                ->where('term_id', $selectedTermId)
                ->whereHas('feeType', fn ($q) => $q->where('type', 'optional')) // ← fix 1
                ->get();
        }

        $selectedFeeId = $request->fee_id;

        // ── Build payments query ──────────────────────────────────
        $payments = collect();
        $summary = null;

        if ($selectedTermId) {
            $query = Payment::with(['student', 'fee.feeType', 'fee.schoolClass', 'receivedBy'])
                ->where('school_id', $schoolId)
                ->whereHas('fee', fn ($q) => $q
                    ->where('term_id', $selectedTermId)
                    ->whereHas('feeType', fn ($fq) => $fq->where('type', 'optional')) // ← fix 2
                )
                ->when($selectedFeeId, fn ($q) => $q->where('fee_id', $selectedFeeId))
                ->when($request->status, fn ($q) => $q->where('status', $request->status))
                ->when($request->search, fn ($q) => $q->whereHas('student', fn ($sq) => $sq->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('last_name', 'like', "%{$request->search}%")
                    ->orWhere('admission_number', 'like', "%{$request->search}%")
                ))
                ->orderByDesc('payment_date');

            // Summary stats
            $all = (clone $query)->get();
            $summary = [
                'total' => $all->count(),
                'paid' => $all->where('status', 'paid')->count(),
                'partial' => $all->where('status', 'partial')->count(),
                'owing' => $all->where('status', 'owing')->count(),
                'total_amount' => $all->sum('amount_paid'),
            ];

            $payments = $query->paginate(20)->withQueryString();
        }

        return view('dashboards.admin.payment.optional', compact(
            'sessions', 'terms', 'fees', 'payments', 'summary',
            'selectedSessionId', 'selectedTermId', 'selectedFeeId',
        ));
    }

    // ────────────────────────────────────────────────────────────────
    // CREATE — Form to record a defaulter payment
    // ────────────────────────────────────────────────────────────────
    public function createDefaulter(Request $request)
    {
        $currentSession = AcademicSession::current()->first();
        $currentTerm = Term::current()->first();

        $students = Student::active()->orderBy('first_name')->get();

        // Only defaulter fees for current term
        $fees = Fee::with(['feeType', 'schoolClass'])
            ->whereHas('feeType', fn ($q) => $q->where('type', 'optional'))
            ->when($currentTerm, fn ($q) => $q->where('term_id', $currentTerm->id))
            ->orderBy('created_at', 'desc')
            ->get();

        $student = $request->student_id
            ? Student::find($request->student_id)
            : null;

        return view('dashboards.admin.payment.defaulter-create', compact(
            'fees', 'students', 'student', 'currentSession', 'currentTerm'
        ));
    }

    // ────────────────────────────────────────────────────────────────
    // STORE — Record a defaulter payment (reuses same store logic)
    // ────────────────────────────────────────────────────────────────
    public function storeDefaulter(Request $request)
    {
        $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'fee_id' => ['required', 'exists:fees,id'],
            'amount_paid' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', Rule::in(['cash', 'transfer', 'pos', 'cheque'])],
            'reference' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $fee = Fee::with('feeType')->findOrFail($request->fee_id); // ← add with('feeType')
        $student = Student::findOrFail($request->student_id);

        abort_if($fee->school_id !== Auth::user()->school_id, 403);
        abort_if($student->school_id !== Auth::user()->school_id, 403);
        abort_if($fee->feeType->type !== 'optional', 403, 'This fee is not an optional fee.'); // ← fixed

        $existing = Payment::where('student_id', $student->id)->where('fee_id', $fee->id)->first();
        $previouslyPaid = $existing?->amount_paid ?? 0;
        $totalPaid = $previouslyPaid + $request->amount_paid;
        $balance = max(0, $fee->amount - $totalPaid);

        $status = match (true) {
            $totalPaid >= $fee->amount => 'paid',
            $totalPaid > 0 => 'partial',
            default => 'owing',
        };

        if ($existing) {
            $existing->update([
                'amount_paid' => $totalPaid,
                'balance' => $balance,
                'status' => $status,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'reference' => $request->reference,
                'note' => $request->note,
                'received_by' => Auth::id(),
            ]);
        } else {
            Payment::create([
                'school_id' => Auth::user()->school_id,
                'student_id' => $student->id,
                'fee_id' => $fee->id,
                'amount_paid' => $request->amount_paid,
                'balance' => $balance,
                'status' => $status,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'reference' => $request->reference,
                'note' => $request->note,
                'received_by' => Auth::id(),
            ]);
        }

        return redirect()->route('admin.payments.defaulter.index')
            ->with('success', 'Defaulter payment of ₦'.number_format($request->amount_paid, 0)." recorded for {$student->first_name} {$student->last_name}.");
    }

    // ── AJAX — terms by session (reuse same pattern) ──────────────
    // public function termsBySession(Request $request)
    // {
    //     $terms = Term::where('school_id', Auth::user()->school_id)
    //         ->where('session_id', $request->session_id)
    //         ->orderByRaw("FIELD(name, 'first', 'second', 'third')")
    //         ->get(['id', 'name', 'is_current']);

    //     return response()->json($terms);
    // }

    // ── AJAX — defaulter fees by term ─────────────────────────────
    public function defaulterFeesByTerm(Request $request)
    {
        $fees = Fee::with('feeType')
            ->where('school_id', Auth::user()->school_id)
            ->where('term_id', $request->term_id)
            ->where('type', 'defaulter')
            ->get()
            ->map(fn ($fee) => [
                'id' => $fee->id,
                'name' => $fee->feeType->name.' — ₦'.number_format($fee->amount, 2)
                          .($fee->schoolClass ? ' ('.$fee->schoolClass->name.')' : ' (All Classes)'),
            ]);

        return response()->json($fees);
    }
}

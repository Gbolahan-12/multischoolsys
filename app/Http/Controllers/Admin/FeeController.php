<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeType;
use App\Models\SchoolClass;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeeController extends Controller
{
    public function index()
    {
        $currentSession = AcademicSession::current()->first();
        $currentTerm    = Term::current()->first();

        $fees = Fee::with(['feeType', 'schoolClass.level', 'schoolClass.section', 'term', 'session'])
            ->when($currentTerm, fn($q) => $q->where('term_id', $currentTerm->id))
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(fn($f) => $f->feeType->name);

        $feeTypes = FeeType::orderBy('name')->get();
        $classes  = SchoolClass::with(['level', 'section'])->orderBy('name')->get();
        $sessions = AcademicSession::orderBy('name', 'desc')->get();
        $terms    = $currentSession
            ? Term::where('session_id', $currentSession->id)->orderBy('name')->get()
            : collect();

        // Summary stats for current term
        $totalFees        = Fee::when($currentTerm, fn($q) => $q->where('term_id', $currentTerm->id))->count();
        $totalCollected   = \App\Models\Payment::when($currentTerm, fn($q) =>
            $q->whereHas('fee', fn($q) => $q->where('term_id', $currentTerm->id))
        )->sum('amount_paid');
        $totalOutstanding = \App\Models\Payment::when($currentTerm, fn($q) =>
            $q->whereHas('fee', fn($q) => $q->where('term_id', $currentTerm->id))
        )->sum('balance');

        return view('dashboards.admin.fee.index', compact(
            'fees', 'feeTypes', 'classes', 'sessions', 'terms',
            'currentSession', 'currentTerm',
            'totalFees', 'totalCollected', 'totalOutstanding'
        ));
    }

    // ── Fee Types ────────────────────────────────────────────

    public function storeFeeType(Request $request)
    {
        // dd($request->all());


        $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('fee_types')->where('school_id', auth()->user()->school_id),
            ],
            'type'        => 'required|in:compulsory,defaulter',
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        FeeType::create([
            'school_id'   => auth()->user()->school_id,
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('success', "Fee type \"{$request->name}\" created.");
    }

    public function updateFeeType(Request $request, FeeType $feeType)
    {
        abort_if($feeType->school_id !== auth()->user()->school_id, 403);

        $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('fee_types')
                    ->where('school_id', auth()->user()->school_id)
                    ->ignore($feeType->id),
            ],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $feeType->update(['name' => $request->name, 'description' => $request->description]);
        return back()->with('success', 'Fee type updated.');
    }

    public function destroyFeeType(FeeType $feeType)
    {
        abort_if($feeType->school_id !== auth()->user()->school_id, 403);

        if ($feeType->fees()->exists()) {
            return back()->with('error', 'Cannot delete a fee type that has fees attached to it.');
        }

        $feeType->delete();
        return back()->with('success', 'Fee type deleted.');
    }

    // ── Fees ─────────────────────────────────────────────────

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'fee_type_id' => ['required', 'exists:fee_types,id'],
            'session_id'  => ['required', 'exists:academic_sessions,id'],
            'term_id'     => ['required', 'exists:terms,id'],
            'class_id'    => ['nullable', 'exists:classes,id'],
            'amount'      => ['required', 'numeric', 'min:1'],
            
            // 'description' => ['nullable', 'string', 'max:255'],
        ]);

        // Prevent duplicate fee for same type+term+class
        $exists = Fee::where('fee_type_id', $request->fee_type_id)
            ->where('term_id', $request->term_id)
            ->where('class_id', $request->class_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'A fee of this type already exists for the selected term and class.');
        }

        Fee::create([
            'school_id'   => auth()->user()->school_id,
            'fee_type_id' => $request->fee_type_id,
            'session_id'  => $request->session_id,
            'term_id'     => $request->term_id,
            'class_id'    => $request->class_id,
            'amount'      => $request->amount,
            // 'description' => $request->description,
        ]);

        return back()->with('success', 'Fee created successfully.');
    }

    public function update(Request $request, Fee $fee)
    {
        abort_if($fee->school_id !== auth()->user()->school_id, 403);

        $request->validate([
            'amount'      => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $fee->update(['amount' => $request->amount, 'description' => $request->description]);
        return back()->with('success', 'Fee updated.');
    }

    public function destroy(Fee $fee)
    {
        abort_if($fee->school_id !== auth()->user()->school_id, 403);

        if ($fee->payments()->exists()) {
            return back()->with('error', 'Cannot delete a fee that has payments recorded against it.');
        }

        $fee->delete();
        return back()->with('success', 'Fee deleted.');
    }

    // AJAX: get terms for a session
    public function termsBySession(AcademicSession $session)
    {
        abort_if($session->school_id !== auth()->user()->school_id, 403);
        return response()->json($session->terms()->orderBy('name')->get(['id', 'name']));
    }
}
<?php

namespace App\Http\Controllers\Proprietor;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SessionTermController extends Controller
{
    // ======================================================
    // SESSIONS
    // ======================================================

    public function index()
    {
        $sessions = AcademicSession::withCount('terms')
            ->with(['terms' => fn($q) => $q->orderBy('name')])
            ->latest()
            ->get();

        $currentSession = AcademicSession::current()->with('currentTerm')->first();

        return view('dashboards.proprietor.sessions.index', compact('sessions', 'currentSession'));
    }

    public function storeSession(Request $request)
    {
        $request->validate([
            'name'       => [
                'required', 'string', 'max:50',
                Rule::unique('academic_sessions')->where('school_id', auth()->user()->school_id),
            ],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
        ]);

        AcademicSession::create([
            'school_id'  => auth()->user()->school_id,
            'name'       => $request->name,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'is_current' => false,
        ]);

        return back()->with('success', "Session \"{$request->name}\" created successfully.");
    }

    public function updateSession(Request $request, AcademicSession $session)
    {
        $this->authorizeSchool($session);

        $request->validate([
            'name'       => [
                'required', 'string', 'max:50',
                Rule::unique('academic_sessions')
                    ->where('school_id', auth()->user()->school_id)
                    ->ignore($session->id),
            ],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
        ]);

        $session->update([
            'name'       => $request->name,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
        ]);

        return back()->with('success', "Session updated successfully.");
    }

    public function setCurrentSession(AcademicSession $session)
    {
        $this->authorizeSchool($session);
        $session->setAsCurrent();

        return back()->with('success', "\"{$session->name}\" is now the active session.");
    }

    public function destroySession(AcademicSession $session)
    {
        $this->authorizeSchool($session);

        if ($session->is_current) {
            return back()->with('error', 'You cannot delete the active session. Set another session as current first.');
        }

        $session->delete();
        return back()->with('success', 'Session deleted successfully.');
    }

    // ======================================================
    // TERMS
    // ======================================================

    public function storeTerm(Request $request, AcademicSession $session)
    {
        $this->authorizeSchool($session);

        $request->validate([
            'name'       => [
                'required',
                Rule::in(['first', 'second', 'third']),
                Rule::unique('terms')->where(fn($q) => $q
                    ->where('session_id', $session->id)
                    ->where('school_id', auth()->user()->school_id)
                ),
            ],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
        ]);

        Term::create([
            'school_id'  => auth()->user()->school_id,
            'session_id' => $session->id,
            'name'       => $request->name,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'is_current' => false,
        ]);

        return back()->with('success', ucfirst($request->name) . " term added to {$session->name}.");
    }

    public function updateTerm(Request $request, Term $term)
    {
        $this->authorizeSchool($term);

        $request->validate([
            'name'       => [
                'required',
                Rule::in(['first', 'second', 'third']),
                Rule::unique('terms')->where(fn($q) => $q
                    ->where('session_id', $term->session_id)
                    ->where('school_id', auth()->user()->school_id)
                )->ignore($term->id),
            ],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
        ]);

        $term->update([
            'name'       => $request->name,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
        ]);

        return back()->with('success', 'Term updated successfully.');
    }

    public function setCurrentTerm(Term $term)
    {
        $this->authorizeSchool($term);
        $term->setAsCurrent(); // also sets parent session as current

        return back()->with('success', ucfirst($term->name) . " term of {$term->session->name} is now active.");
    }

    public function destroyTerm(Term $term)
    {
        $this->authorizeSchool($term);

        if ($term->is_current) {
            return back()->with('error', 'You cannot delete the active term.');
        }

        $term->delete();
        return back()->with('success', 'Term deleted successfully.');
    }

    // ======================================================
    // HELPERS
    // ======================================================

    private function authorizeSchool($model): void
    {
        abort_if($model->school_id !== auth()->user()->school_id, 403);
    }
}
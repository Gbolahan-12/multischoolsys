<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Result;
use App\Models\Term;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $staff = Auth::user();
        $currentSession = AcademicSession::current()->first();
        $currentTerm = Term::current()->first();

        // ── Assigned subjects from class_subject_assignments ─────
        // This is the source of truth — not uploaded results
        $assignments = \App\Models\ClassSubjectAssignment::with(['subject', 'schoolClass'])
            ->where('teacher_id', $staff->id)
            ->when($currentSession, fn ($q) => $q->where('session_id', $currentSession->id))
            ->get();

        // ── Uploaded results this term (for checking component status)
        $uploadedResults = Result::where('uploaded_by', $staff->id)
            ->when($currentTerm, fn ($q) => $q->where('term_id', $currentTerm->id))
            ->get();

        $palette = ['#0d6efd', '#198754', '#0dcaf0', '#ffc107', '#6f42c1', '#fd7e14', '#dc3545', '#20c997'];

        $mySubjects = $assignments->map(function ($assignment, $i) use ($uploadedResults, $palette) {
            $subset = $uploadedResults
                ->where('class_id', $assignment->class_id)
                ->where('subject_id', $assignment->subject_id);

            return [
                'subject' => $assignment->subject->name ?? '—',
                'class' => $assignment->schoolClass->full_name
                             ?? $assignment->schoolClass->name
                             ?? '—',
                'color' => $palette[$i % count($palette)],
                'uploaded' => [
                    'ca1' => $subset->whereNotNull('ca1_score')->isNotEmpty(),
                    'ca2' => $subset->whereNotNull('ca2_score')->isNotEmpty(),
                    'exam' => $subset->whereNotNull('exam_score')->isNotEmpty(),
                ],
            ];
        });

        $totalResultsUploaded = Result::where('uploaded_by', $staff->id)
            ->when($currentTerm, fn ($q) => $q->where('term_id', $currentTerm->id))
            ->count();

        // ── Pending: assigned subjects where at least one component missing
        $pendingUploads = $assignments->filter(function ($assignment) use ($uploadedResults) {
            $subset = $uploadedResults
                ->where('class_id', $assignment->class_id)
                ->where('subject_id', $assignment->subject_id);

            // Pending if nothing uploaded at all OR any component is missing
            return $subset->isEmpty()
                || $subset->whereNull('ca1_score')->isNotEmpty()
                || $subset->whereNull('ca2_score')->isNotEmpty()
                || $subset->whereNull('exam_score')->isNotEmpty();
        })->count();

        // ── Recent results (last 15)
        $recentResults = Result::where('uploaded_by', $staff->id)
            ->when($currentTerm, fn ($q) => $q->where('term_id', $currentTerm->id))
            ->with(['student', 'subject', 'schoolClass'])
            ->latest('updated_at')
            ->limit(15)
            ->get();

        return view('dashboards.staff.dashboard', compact(
            'currentSession', 'currentTerm',
            'mySubjects', 'totalResultsUploaded',
            'pendingUploads', 'recentResults',
            'assignments' // pass assignments too for the subjects page
        ));
    }
}

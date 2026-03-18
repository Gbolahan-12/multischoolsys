<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ClassSubjectAssignment;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportCardController extends Controller
{
    public function index()
{
    $schoolId = Auth::user()->school_id;
    $staff    = Auth::user();

    $sessions = AcademicSession::where('school_id', $schoolId)
        ->orderByDesc('created_at')->get();

    $currentSession = AcademicSession::current()->first();
    $currentTerm    = Term::where('school_id', $schoolId)
        ->where('is_current', true)
        ->with('session')->first();

    // Only classes where this teacher has been assigned a subject
    $assignedClassIds = ClassSubjectAssignment::where('teacher_id', $staff->id)
        ->when($currentSession, fn($q) => $q->where('session_id', $currentSession->id))
        ->pluck('class_id')
        ->unique();

    $classes = SchoolClass::where('school_id', $schoolId)
        ->whereIn('id', $assignedClassIds)
        ->orderBy('name')
        ->get();

    return view('dashboards.staff.report-cards.index', compact(
        'sessions', 'classes', 'currentTerm'
    ));
}

    public function preview(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id',
        ]);

        $data = $this->buildReportData(
            Auth::user()->school_id,
            $request->class_id,
            $request->term_id
        );

        if ($data['students']->isEmpty()) {
            return back()->with('error', 'No results found for this class and term.');
        }

        return view('dashboards.staff.report-cards.preview', $data);
    }

    public function download(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id',
        ]);

        $data = $this->buildReportData(
            Auth::user()->school_id,
            $request->class_id,
            $request->term_id
        );

        if ($data['students']->isEmpty()) {
            return back()->with('error', 'No results found for this class and term.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboards.staff.report-cards.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isRemoteEnabled' => true,
                'isLocalEnabled' => true,
                'chroot' => public_path(),
                'dpi' => 150,
            ]);

        $filename = sprintf(
            'report-cards-%s-%s-term.pdf',
            str_replace(' ', '-', strtolower($data['class']->name)),
            strtolower($data['term']->name)
        );

        return $pdf->download($filename);
    }

    private function buildReportData(int $schoolId, int $classId, int $termId): array
    {
        $class = SchoolClass::findOrFail($classId);
        $term = Term::with('session')->findOrFail($termId);
        $school = Auth::user()->school;

        // Students who have results in this class+term
        $studentIds = Result::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('term_id', $termId)
            ->distinct()->pluck('student_id');

        $students = Student::whereIn('id', $studentIds)
            ->where('school_id', $schoolId)
            ->orderBy('last_name')->get();

        // Subjects for this class+term
        $subjectIds = Result::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('term_id', $termId)
            ->distinct()->pluck('subject_id');

        $subjects = Subject::whereIn('id', $subjectIds)
            ->orderBy('name')->get();

        // Result map [student_id][subject_id] => result
        $resultsRaw = Result::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('term_id', $termId)->get();

        $resultMap = [];
        foreach ($resultsRaw as $result) {
            $resultMap[$result->student_id][$result->subject_id] = $result;
        }

        $classAverages = [];
        foreach ($subjects as $subject) {
            $totalScores = [];

            foreach ($students as $student) {
                $r = $resultMap[$student->id][$subject->id] ?? null;
                if ($r && ! is_null($r->total_score)) {
                    $totalScores[] = (float) $r->total_score;
                }
            }

            $classAverages[$subject->id] = count($totalScores) > 0
                ? round(array_sum($totalScores) / count($totalScores), 1)
                : null;
        }
        // Calculate totals and positions
        $studentTotals = [];
        foreach ($students as $student) {
            $total = 0;
            foreach ($subjects as $subject) {
                $r = $resultMap[$student->id][$subject->id] ?? null;
                $total += $r ? ($r->total_score ?? 0) : 0;
            }
            $studentTotals[$student->id] = round($total, 2);
        }

        arsort($studentTotals);
        $positions = [];
        $pos = 1;
        $prevTotal = null;
        $prevPos = 1;
        foreach ($studentTotals as $sid => $total) {
            if ($total === $prevTotal) {
                $positions[$sid] = $prevPos;
            } else {
                $positions[$sid] = $pos;
                $prevPos = $pos;
            }
            $prevTotal = $total;
            $pos++;
        }

        $gradeSystem = [
            ['min' => 75, 'max' => 100, 'grade' => 'A',  'remark' => 'Excellent'],
            ['min' => 65, 'max' => 74,  'grade' => 'B',  'remark' => 'Very Good'],
            ['min' => 55, 'max' => 64,  'grade' => 'C',  'remark' => 'Good'],
            ['min' => 45, 'max' => 54,  'grade' => 'D',  'remark' => 'Pass'],
            ['min' => 40, 'max' => 44,  'grade' => 'E',  'remark' => 'Fair'],
            ['min' => 0,  'max' => 39,  'grade' => 'F',  'remark' => 'Fail'],
        ];
        $classPopulation = $students->count();

        return compact(
            'school', 'class', 'term', 'students',
            'subjects', 'resultMap', 'positions',
            'studentTotals', 'gradeSystem', 'classAverages',
            'classPopulation'
        );
    }

    public function termsBySession(Request $request)
    {
        $terms = Term::where('school_id', Auth::user()->school_id)
            ->where('session_id', $request->session_id)
            ->orderByRaw("FIELD(name, 'first', 'second', 'third')")
            ->get(['id', 'name', 'is_current', 'end_date']);

        return response()->json($terms);
    }
}

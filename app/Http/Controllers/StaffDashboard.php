<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\ClassSubjectAssignment;
use App\Models\Result;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SingleResultUpload;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffDashboard extends Controller
{
    //
    public function index()
    {
        return view('dashboards.staff.index');
    }
    public function uploadResultForm()
    {
        $schools = School::all();
        // $classes = SchoolClass::all();
        // $students = Student::with('user')->get();
        $subjects = Subject::all();

        return view('dashboards.staff.result.upload-result', compact(
            'schools',
            'subjects'
        ));
    }

    public function classesBySchool(School $school)
    {
        $classes = $school->classes()->select('id', 'name')->orderBy('name')->get();
        return response()->json($classes);
    }

    public function subjectsBySchool(School $school)
    {
        $subjects = $school->subjects()->select('id', 'name')->orderBy('name')->get();
        return response()->json($subjects);
    }

    // Students by class
    // public function studentsByClass(SchoolClass $class)
    // {
    //     $students = $class->students()->select('id','fullname')->orderBy('name')->get();
    //     return response()->json([
    //     'class_id' => $class->id,
    //     'students_count' => $students->count(),
    //     'students' => $students
    // ]);
    // }


    public function studentsByClass($classId)
    {
        // Try finding the class manually
        $class = SchoolClass::find($classId);
        if (!$class) {
            return response()->json(['error' => 'Class not found'], 404);
        }

        // Try fetching students
        try {
            $students = $class->students()->select('id', 'fullname')->orderBy('fullname')->get();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Query failed',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json($students);
    }

    public function myUploadedResults(Request $request)
{
    $currentSession = AcademicSession::current()->first();
    $currentTerm    = Term::current()->first();

    // No class/subject selected yet — show assignments list
    if (!$request->class_id || !$request->subject_id) {
        $assignments = ClassSubjectAssignment::with(['schoolClass.level', 'schoolClass.section', 'subject'])
            ->where('teacher_id', Auth::id())
            ->when($currentSession, fn($q) => $q->where('session_id', $currentSession->id))
            ->get();

        $uploadedCounts = Result::where('uploaded_by', Auth::id())
            ->when($currentTerm, fn($q) => $q->where('term_id', $currentTerm->id))
            ->selectRaw('class_id, subject_id, COUNT(*) as count')
            ->groupBy('class_id', 'subject_id')
            ->get()
            ->keyBy(fn($r) => $r->class_id . '_' . $r->subject_id);

        return view('dashboards.staff.result.indexing', compact(
            'assignments', 'uploadedCounts', 'currentSession', 'currentTerm'
        ));
    }

    // class_id and subject_id provided — show upload form
    $assignment = ClassSubjectAssignment::where('teacher_id', Auth::id())
        ->where('class_id',   $request->class_id)
        ->where('subject_id', $request->subject_id)
        ->when($currentSession, fn($q) => $q->where('session_id', $currentSession->id))
        ->with(['schoolClass.level', 'schoolClass.section', 'subject'])
        ->firstOrFail();

    $students = Student::whereHas('classAssignments', fn($q) =>
        $q->where('class_id',   $request->class_id)
          ->when($currentSession, fn($q) => $q->where('session_id', $currentSession->id))
          ->when($currentTerm,    fn($q) => $q->where('term_id',    $currentTerm->id))
    )
    ->active()
    ->orderBy('last_name')
    ->get();

    $existingResults = Result::where('class_id',   $request->class_id)
        ->where('subject_id', $request->subject_id)
        ->when($currentTerm, fn($q) => $q->where('term_id', $currentTerm->id))
        ->get()
        ->keyBy('student_id');

    $class   = $assignment->schoolClass;
    $subject = $assignment->subject;

    return view('dashboards.staff.result.upload', compact(
        'assignment', 'class', 'subject', 'students',
        'existingResults', 'currentSession', 'currentTerm'
    ));
}


//     public function myUploadedResults(Request $request)
// {
//     $currentSession = AcademicSession::current()->first();
//     $currentTerm    = Term::current()->first();

//     // No class/subject selected yet — show assignments list
//     if (!$request->class_id || !$request->subject_id) {
//         $assignments = ClassSubjectAssignment::with(['schoolClass.level', 'schoolClass.section', 'subject'])
//             ->where('teacher_id', Auth::id())
//             ->when($currentSession, fn($q) => $q->where('session_id', $currentSession->id))
//             ->get();

//         $uploadedCounts = Result::where('uploaded_by', Auth::id())
//             ->when($currentTerm, fn($q) => $q->where('term_id', $currentTerm->id))
//             ->selectRaw('class_id, subject_id, COUNT(*) as count')
//             ->groupBy('class_id', 'subject_id')
//             ->get()
//             ->keyBy(fn($r) => $r->class_id . '_' . $r->subject_id);

//         return view('dashboards.staff.result.upload', compact(
//             'assignments', 'uploadedCounts', 'currentSession', 'currentTerm'
//         ));
//     }

//     // class_id and subject_id provided — show upload form
//     $assignment = ClassSubjectAssignment::where('teacher_id', Auth::id())
//         ->where('class_id',   $request->class_id)
//         ->where('subject_id', $request->subject_id)
//         ->when($currentSession, fn($q) => $q->where('session_id', $currentSession->id))
//         ->with(['schoolClass.level', 'schoolClass.section', 'subject'])
//         ->firstOrFail();

//     $students = Student::whereHas('classAssignments', fn($q) =>
//         $q->where('class_id',   $request->class_id)
//           ->when($currentSession, fn($q) => $q->where('session_id', $currentSession->id))
//           ->when($currentTerm,    fn($q) => $q->where('term_id',    $currentTerm->id))
//     )
//     ->active()
//     ->orderBy('last_name')
//     ->get();

//     $existingResults = Result::where('class_id',   $request->class_id)
//         ->where('subject_id', $request->subject_id)
//         ->when($currentTerm, fn($q) => $q->where('term_id', $currentTerm->id))
//         ->get()
//         ->keyBy('student_id');

//     $class   = $assignment->schoolClass;
//     $subject = $assignment->subject;

//     return view('dashboards.staff.result.upload', compact(
//         'assignment', 'class', 'subject', 'students',
//         'existingResults', 'currentSession', 'currentTerm'
//     ));
// }

}

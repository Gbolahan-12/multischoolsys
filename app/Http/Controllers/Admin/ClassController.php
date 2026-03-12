<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ClassLevel;
use App\Models\ClassSection;
use App\Models\ClassSubjectAssignment;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    public function index()
    {
        $currentSession = AcademicSession::current()->first();

        $classes = SchoolClass::with([
            'formTeacher',
            'level',
            'section',
            'subjectAssignments.subject',
            'subjectAssignments.teacher',
        ])
            ->withCount(['studentAssignments as students_count' => function ($q) use ($currentSession) {
                $q->when($currentSession, fn ($q) => $q->where('session_id', $currentSession->id));
            }])
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($c) => $c->level?->name ?? 'Uncategorised');

        $staff = User::whereIn('role', ['staff'])
            ->where('school_id', Auth::user()->school_id)
            ->where('is_active', true)
            ->orderBy('fullname')
            ->get();
        $levels = ClassLevel::orderBy('name')->get();
        $sections = ClassSection::orderBy('name')->get();

        return view('dashboards.admin.classes.index', compact('classes', 'staff', 'levels', 'sections', 'currentSession'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'level_id' => ['nullable', 'exists:class_levels,id'],
            'section_id' => ['nullable', 'exists:class_sections,id'],
            'form_teacher_id' => ['nullable', 'exists:users,id'],
        ]);

        $exists = SchoolClass::where('name', $request->name)
            ->where('section_id', $request->section_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'A class with this name and section already exists.')->withInput();
        }

        SchoolClass::create([
            'school_id' => Auth::user()->school_id,
            'name' => $request->name,
            'level_id' => $request->level_id,
            'section_id' => $request->section_id,
            'form_teacher_id' => $request->form_teacher_id,
        ]);

        return back()->with('success', 'Class created successfully.');
    }

    public function update(Request $request, SchoolClass $class)
    {
        $this->authorizeSchool($class);

        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'level_id' => ['nullable', 'exists:class_levels,id'],
            'section_id' => ['nullable', 'exists:class_sections,id'],
            'form_teacher_id' => ['nullable', 'exists:users,id'],
        ]);

        $class->update([
            'name' => $request->name,
            'level_id' => $request->level_id,
            'section_id' => $request->section_id,
            'form_teacher_id' => $request->form_teacher_id,
        ]);

        return back()->with('success', 'Class updated successfully.');
    }

    public function destroy(SchoolClass $class)
    {
        $this->authorizeSchool($class);

        if ($class->studentAssignments()->exists()) {
            return back()->with('error', 'Cannot delete a class that has students assigned to it.');
        }

        $class->delete();

        return back()->with('success', 'Class deleted successfully.');
    }

    // ── Assign Subject to Class ──────────────────────────────

    public function assignSubject(Request $request, SchoolClass $class)
    {
        $this->authorizeSchool($class);

        $currentSession = AcademicSession::current()->first();

        if (! $currentSession) {
            return back()->with('error', 'No active session. Please set an active session first.');
        }

        $request->validate([
            'subject_id' => [
                'required', 'exists:subjects,id',
                Rule::unique('class_subject_assignments')->where(fn ($q) => $q
                    ->where('class_id', $class->id)
                    ->where('session_id', $currentSession->id)
                ),
            ],
            'teacher_id' => ['nullable', 'exists:users,id'],
        ]);

        ClassSubjectAssignment::create([
            'class_id' => $class->id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'session_id' => $currentSession->id,
        ]);

        return back()->with('success', 'Subject assigned successfully.');
    }

    public function removeSubject(ClassSubjectAssignment $assignment)
    {
        $this->authorizeSchool($assignment->schoolClass);
        $assignment->delete();

        return back()->with('success', 'Subject removed from class.');
    }

    private function authorizeSchool($model): void
    {
        abort_if($model->school_id !== Auth::user()->school_id, 403);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentClassAssignment;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $currentSession = AcademicSession::current()->first();
        $currentTerm = Term::current()->first();
        $classes = SchoolClass::with(['level', 'section'])->orderBy('name')->get();

        $students = Student::with(['currentAssignment.schoolClass'])
            ->when($request->search, fn ($q) => $q->where(fn ($q) => $q->where('first_name', 'like', "%{$request->search}%")
                ->orWhere('last_name', 'like', "%{$request->search}%")
                ->orWhere('admission_number', 'like', "%{$request->search}%")
            )
            )
            ->when($request->class_id, fn ($q) => $q->whereHas('classAssignments', fn ($q) => $q->where('class_id', $request->class_id)
                ->when($currentSession, fn ($q) => $q->where('session_id', $currentSession->id))
            )
            )
            ->when($request->status === 'active', fn ($q) => $q->active())
            ->when($request->status === 'inactive', fn ($q) => $q->inactive())
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('dashboards.admin.student.index', compact(
            'students', 'classes', 'currentSession', 'currentTerm'
        ));
    }

    public function create()
    {
        $classes = SchoolClass::with(['level', 'section'])->orderBy('name')->get();
        $session = AcademicSession::current()->first();
        $term = Term::current()->first();

        return view('dashboards.admin.student.create', compact('classes', 'session', 'term'));
    }

    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'other_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'guardian_phone' => ['required', 'string', 'max:20'],
            'guardian_email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'admission_number' => [
                'nullable', 'string', 'max:50',
                Rule::unique('students')->where('school_id', $schoolId),
            ],
        ]);

        $student = Student::create([
            'school_id' => $schoolId,
            'admission_number' => $request->admission_number
                                    ?: Student::generateAdmissionNumber($schoolId),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'other_name' => $request->other_name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'guardian_name' => $request->guardian_name,
            'guardian_phone' => $request->guardian_phone,
            'guardian_email' => $request->guardian_email,
            'address' => $request->address,
            'is_active' => true,
        ]);

        // Assign to class if selected and current session/term exist
        if ($request->class_id) {
            $session = AcademicSession::current()->first();
            $term = Term::current()->first();

            if ($session && $term) {
                StudentClassAssignment::create([
                    'student_id' => $student->id,
                    'class_id' => $request->class_id,
                    'session_id' => $session->id,
                    'term_id' => $term->id,
                ]);
            }
        }

        return redirect()->route('admin.students.show', $student)
            ->with('success', "Student {$student->full_name} created successfully.");
    }

    public function show(Student $student)
    {
        $this->authorizeSchool($student);
        $student->load([
            'classAssignments.schoolClass.level',
            'classAssignments.schoolClass.section',
            'classAssignments.session',
            'classAssignments.term',
            'payments.fee.feeType',
        ]);

        $classes = SchoolClass::with(['level', 'section'])->orderBy('name')->get();
        $session = AcademicSession::current()->first();
        $term = Term::current()->first();

        return view('dashboards.admin.student.show', compact('student', 'classes', 'session', 'term'));
    }

    public function edit(Student $student)
    {
        $this->authorizeSchool($student);

        return view('dashboards.admin.student.edit', compact('student'));
    }

    // public function update(Request $request, Student $student)
    // {
    //     $this->authorizeSchool($student);

    //     $request->validate([
    //         'first_name' => ['required', 'string', 'max:100'],
    //         'last_name' => ['required', 'string', 'max:100'],
    //         'other_name' => ['nullable', 'string', 'max:100'],
    //         'gender' => ['required', Rule::in(['male', 'female'])],
    //         'date_of_birth' => ['nullable', 'date', 'before:today'],
    //         'guardian_name' => ['required', 'string', 'max:255'],
    //         'guardian_phone' => ['required', 'string', 'max:20'],
    //         'guardian_email' => ['nullable', 'email'],
    //         'address' => ['nullable', 'string', 'max:500'],
    //         'admission_number' => [
    //             'required', 'string', 'max:50',
    //             Rule::unique('students')
    //                 ->where('school_id', Auth::user()->school_id)
    //                 ->ignore($student->id),
    //         ],
    //     ]);

    //     $student->update($request->only([
    //         'first_name', 'last_name', 'other_name', 'gender',
    //         'date_of_birth', 'guardian_name', 'guardian_phone',
    //         'guardian_email', 'address', 'admission_number',
    //     ]));

    //     return redirect()->route('admin.students.show', $student)
    //         ->with('success', 'Student updated successfully.');
    // }

    public function update(Request $request, Student $student)
    {
        $this->authorizeSchool($student);

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'other_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'guardian_name' => ['required', 'string', 'max:255'],
            'guardian_phone' => ['required', 'string', 'max:20'],
            'guardian_email' => ['nullable', 'email'],
            'address' => ['nullable', 'string', 'max:500'],
            'admission_number' => [
                'required', 'string', 'max:50',
                Rule::unique('students')
                    ->where('school_id', Auth::user()->school_id)
                    ->ignore($student->id),
            ],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);
        $photoPath = $student->photo;

        if ($request->boolean('remove_photo') && $student->photo) {
            if (\Storage::disk('public')->exists($student->photo)) {
                \Storage::disk('public')->delete($student->photo);
            }
            $photoPath = null;
        }

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($student->photo && \Storage::disk('public')->exists($student->photo)) {
                \Storage::disk('public')->delete($student->photo);
            }
            $photoPath = $request->file('photo')->store('student-photos', 'public');
        }

        // ── Update student ────────────────────────────────────────
        $student->update(array_merge(
            $request->only([
                'first_name', 'last_name', 'other_name', 'gender',
                'date_of_birth', 'guardian_name', 'guardian_phone',
                'guardian_email', 'address', 'admission_number',
            ]),
            ['photo' => $photoPath]
        ));

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Student updated successfully.');
    }

    // Assign student to a class for current session/term
    public function assignClass(Request $request, Student $student)
    {
        $this->authorizeSchool($student);

        $session = AcademicSession::current()->first();
        $term = Term::current()->first();

        if (! $session || ! $term) {
            return back()->with('error', 'No active session or term. Please set one first.');
        }

        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
        ]);

        // Remove existing assignment for this term
        StudentClassAssignment::where('student_id', $student->id)
            ->where('session_id', $session->id)
            ->where('term_id', $term->id)
            ->delete();

        StudentClassAssignment::create([
            'student_id' => $student->id,
            'class_id' => $request->class_id,
            'session_id' => $session->id,
            'term_id' => $term->id,
        ]);

        return back()->with('success', 'Student assigned to class successfully.');
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $students = Student::where(function ($query) use ($q) {
            $query->where('first_name', 'like', "%{$q}%")
                ->orWhere('last_name', 'like', "%{$q}%")
                ->orWhere('admission_number', 'like', "%{$q}%");
        })
            ->active()
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'admission_number']);

        return response()->json($students);
    }

    public function toggleStatus(Student $student)
    {
        $this->authorizeSchool($student);
        $student->update(['is_active' => ! $student->is_active]);
        $status = $student->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "{$student->full_name} has been {$status}.");
    }

    public function destroy(Student $student)
    {
        $this->authorizeSchool($student);
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    // ── Excel Import ─────────────────────────────────────────

    public function importForm()
    {

        $classes = SchoolClass::where('school_id', Auth::user()->school_id)
            ->with('section')
            ->orderBy('name')
            ->get();

        return view('dashboards.admin.student.import', compact('classes'));
    }

    public function import(Request $request)
{
    $request->validate([
        'file'     => ['required', 'file', 'max:5120', function ($attribute, $value, $fail) {
            $ext = strtolower($value->getClientOriginalExtension());
            if (!in_array($ext, ['csv', 'xlsx', 'xls'])) {
                $fail('The file must be a CSV, XLSX or XLS file.');
            }
        }],
        'class_id' => ['nullable', 'exists:classes,id'],
    ]);

    try {
        $import = new StudentsImport(
            Auth::user()->school_id,
            $request->class_id // ← pass class_id
        );
        $import->import($request->file('file'));

        $imported = $import->getRowCount();
        $errors   = $import->getErrors();

        if (count($errors)) {
            return back()
                ->with('import_errors', $errors)
                ->with('import_count', $imported)
                ->with('warning', "{$imported} students imported. " . count($errors) . ' rows had errors.');
        }

        return redirect()->route('admin.students.index')
            ->with('success', "{$imported} students imported successfully.");

    } catch (\Exception $e) {
        return back()->with('error', 'Import failed: ' . $e->getMessage());
    }
}

    public function downloadTemplate()
{
    $headers = [
        'first_name', 'last_name', 'other_name', 'gender', 'date_of_birth',
        'guardian_name', 'guardian_phone', 'guardian_email', 'address', 'admission_number',
    ];

    $callback = function () use ($headers) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $headers);
        fputcsv($file, [
            'John', 'Doe', '', 'male', '2010-05-15',
            'Jane Doe', '08012345678', 'jane@email.com',
            '123 Street, Lagos', '',
        ]);
        fclose($file);
    };

    return response()->stream($callback, 200, [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => 'attachment; filename="students_import_template.csv"',
    ]);
}

    private function authorizeSchool(Student $student): void
    {
        abort_if($student->school_id !== Auth::user()->school_id, 403);
    }
}

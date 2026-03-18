<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\ClassSubjectAssignment;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminDashboardController extends Controller
{
    //
    public function index()
    {
        $currentSession = AcademicSession::current()->first();
        $currentTerm = Term::current()->first();
        $schoolId = Auth::user()->school_id;

        $totalStudents = Student::active()->count();

        $totalStaff = User::where('role', 'staff')
            ->whereNull('banned_at')
            ->count();

        $feesCollected = Payment::when($currentTerm, fn ($q) => $q->whereHas('fee', fn ($q) => $q->where('term_id', $currentTerm->id)
        ))->sum('amount_paid');

        // ── Compulsory fee IDs for current term ───────────────────
        $compulsoryFeeIds = $currentTerm
            ? Fee::where('school_id', $schoolId)
                ->where('term_id', $currentTerm->id)
                ->whereHas('feeType', fn ($q) => $q->where('type', 'compulsory'))
                ->pluck('id')
            : collect();

        // ── Students owing (compulsory only) ─────────────────────
        // Partial/owing payments + students with NO payment record at all
        $studentsOwing = 0;

        if ($currentTerm && $compulsoryFeeIds->isNotEmpty()) {

            // Students who have made any payment for a compulsory fee
            $studentsThatPaid = Payment::where('school_id', $schoolId)
                ->whereIn('fee_id', $compulsoryFeeIds)
                ->pluck('student_id')
                ->unique();

            // Students with partial or owing payment status
            $partialStudentIds = Payment::where('school_id', $schoolId)
                ->whereIn('fee_id', $compulsoryFeeIds)
                ->whereIn('status', ['partial', 'owing'])
                ->pluck('student_id')
                ->unique();

            // Active students with NO payment record at all
            $noPaymentStudentIds = Student::active()
                ->whereNotIn('id', $studentsThatPaid)
                ->pluck('id');

            $studentsOwing = $partialStudentIds
                ->merge($noPaymentStudentIds)
                ->unique()
                ->count();
        }

        $resultsUploaded = Result::when($currentTerm, fn ($q) => $q->where('term_id', $currentTerm->id)
        )->count();

        // ── Recent Payments ───────────────────────────────────────
        $recentPayments = Payment::with(['student', 'fee.feeType'])
            ->when($currentTerm, fn ($q) => $q->whereHas('fee', fn ($q) => $q->where('term_id', $currentTerm->id)
            ))
            ->latest('payment_date')
            ->limit(8)
            ->get();

        // ── Chart 1: Fee collection by month (last 6 months) ─────
        $feeByMonth = Payment::selectRaw("DATE_FORMAT(payment_date, '%b %Y') as month, DATE_FORMAT(payment_date, '%Y-%m') as sort_key, SUM(amount_paid) as total")
            ->where('payment_date', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month', 'sort_key')
            ->orderBy('sort_key')
            ->get();

        $feeChartLabels = $feeByMonth->pluck('month');
        $feeChartData = $feeByMonth->pluck('total');

        // ── Chart 2: Payment status donut (compulsory only) ───────
        $paymentStatus = Payment::where('school_id', $schoolId)
            ->when($compulsoryFeeIds->isNotEmpty(), fn ($q) => $q->whereIn('fee_id', $compulsoryFeeIds))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $statusPaid = $paymentStatus['paid'] ?? 0;
        $statusPartial = $paymentStatus['partial'] ?? 0;
        $statusOwing = $paymentStatus['owing'] ?? 0;

        // Add no-payment students to owing count in chart
        $noPaymentCount = $currentTerm && $compulsoryFeeIds->isNotEmpty()
            ? Student::active()
                ->whereNotIn('id',
                    Payment::where('school_id', $schoolId)
                        ->whereIn('fee_id', $compulsoryFeeIds)
                        ->pluck('student_id')
                )
                ->count()
            : 0;

        $statusOwing += $noPaymentCount;

        // ── Chart 3: Student enrollment growth (last 6 months) ───
        $enrollmentByMonth = Student::selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, DATE_FORMAT(created_at, '%Y-%m') as sort_key, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month', 'sort_key')
            ->orderBy('sort_key')
            ->get();

        $enrollmentLabels = $enrollmentByMonth->pluck('month');
        $enrollmentData = $enrollmentByMonth->pluck('total');

        // ── Chart 4: Grade distribution ───────────────────────────
        $gradeDistribution = Result::when($currentTerm, fn ($q) => $q->where('term_id', $currentTerm->id)
        )
            ->selectRaw('grade, COUNT(*) as count')
            ->whereNotNull('grade')
            ->groupBy('grade')
            ->pluck('count', 'grade');

        $grades = ['A', 'B', 'C', 'D', 'E', 'F'];
        $gradeCounts = collect($grades)->map(fn ($g) => $gradeDistribution[$g] ?? 0);

        return view('dashboards.admin.index', compact(
            'currentSession', 'currentTerm',
            'totalStudents', 'totalStaff', 'feesCollected',
            'studentsOwing', 'resultsUploaded', 'recentPayments',
            'feeChartLabels', 'feeChartData',
            'statusPaid', 'statusPartial', 'statusOwing',
            'enrollmentLabels', 'enrollmentData',
            'grades', 'gradeCounts'
        ));
    }

    public function staffList()
    {
        $users = User::with('profile')
            ->whereIn('role', ['admin', 'staff'])
            ->latest()
            ->paginate(20);

        return view('dashboards.admin.users.index', compact('users'));
    }

    /**
     * Show the form to create a new admin or staff.
     */
    public function staffCreate()
    {
        return view('dashboards.admin.users.create');
    }

    /**
     * Store a new admin or staff account.
     */
    public function staffStore(Request $request)
    {
        // dd(User::generateStaffId(Auth::user()->school_id));
        $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users')->where('school_id', Auth::user()->school_id),
            ],
            'role' => ['required', Rule::in(['admin', 'staff'])],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:500'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'employment_date' => ['nullable', 'date'],
        ]);

        $tempPassword = Str::random(10);

        $user = DB::transaction(function () use ($request) {

            $user = User::create([
                'school_id' => Auth::user()->school_id,
                'staff_id' => User::generateStaffId(Auth::user()->school_id),
                'fullname' => $request->fullname,
                'email' => $request->email,
                'password' => Hash::make('password'),
                'role' => $request->role,
                'is_active' => true,
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'qualification' => $request->qualification,
                'employment_date' => $request->employment_date,
            ]);

            return $user;
        });

        // Flash the temp password once — it won't be retrievable again
        return redirect()->route('admin.users.index')
            ->with('success', "Account created for {$user->fullname}.")
            ->with('temp_password', $tempPassword)
            ->with('staff_id', $user->staff_id)
            ->with('temp_email', $user->email);
    }

    public function show(User $user)
    {
        $this->authorizeSchoolAccess($user);
        $user->load('profile');

        return view('dashboards.admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorizeSchoolAccess($user);
        $user->load('profile');

        return view('dashboards.admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeSchoolAccess($user);

        $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users')->where('school_id', Auth::user()->school_id)->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['admin', 'staff'])],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:500'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'employment_date' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'name' => $request->fullname,
                'email' => $request->email,
                'role' => $request->role,
            ]);

            $user->profile->update([
                'phone' => $request->phone,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'qualification' => $request->qualification,
                'employment_date' => $request->employment_date,
            ]);
        });

        return redirect()->route('admin.users.index')
            ->with('success', "{$user->fullname}'s account has been updated.");
    }

    public function ban(User $user)
    {
        $this->authorizeSchoolAccess($user);

        $user->update([
            'is_active' => false,
            'banned_at' => now(),
            'banned_by' => Auth::id(),
        ]);

        return back()->with('success', "{$user->fullname} has been banned.");
    }

    /**
     * Unban a user.
     */
    public function unban(User $user)
    {
        $this->authorizeSchoolAccess($user);

        $user->update([
            'is_active' => true,
            'banned_at' => null,
            'banned_by' => null,
        ]);

        return back()->with('success', "{$user->fullname} has been unbanned.");
    }

    public function destroy(User $user)
    {
        $this->authorizeSchoolAccess($user);
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Account deleted successfully.');
    }

    public function indexResult()
    {
        $currentSession = AcademicSession::current()->first();
        $currentTerm = Term::current()->first();
        $classes = SchoolClass::with(['level', 'section'])->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('dashboards.admin.results.indexing', compact(
            'classes', 'subjects', 'currentSession', 'currentTerm'
        ));
    }

    public function uploadForm(Request $request)
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
        ]);

        $currentSession = AcademicSession::current()->first();
        $currentTerm = Term::current()->first();

        $class = SchoolClass::with(['level', 'section'])->findOrFail($request->class_id);
        $subject = Subject::findOrFail($request->subject_id);

        $students = Student::whereHas('classAssignments', fn ($q) => $q->where('class_id', $request->class_id)
            ->when($currentSession, fn ($q) => $q->where('session_id', $currentSession->id))
            ->when($currentTerm, fn ($q) => $q->where('term_id', $currentTerm->id))
        )
            ->active()
            ->orderBy('last_name')
            ->get();

        $existingResults = Result::where('class_id', $request->class_id)
            ->where('subject_id', $request->subject_id)
            ->when($currentTerm, fn ($q) => $q->where('term_id', $currentTerm->id))
            ->get()
            ->keyBy('student_id');

        return view('dashboards.admin.results.upload', compact(
            'class', 'subject', 'students',
            'existingResults', 'currentSession', 'currentTerm'
        ));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimetypes:text/csv,text/plain,application/csv,application/vnd.ms-excel',
            'class_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'component' => ['required', 'in:ca1,ca2,exam'],
        ]);

        $currentSession = AcademicSession::current()->first();
        $currentTerm = Term::current()->first();

        if (! $currentSession || ! $currentTerm) {
            return back()->with('error', 'No active session or term. Contact your admin.');
        }

        $component = $request->component;
        $scoreKey = "{$component}_score";
        $maxScore = match ($component) {
            'ca1' => Result::MAX_CA1,
            'ca2' => Result::MAX_CA2,
            'exam' => Result::MAX_EXAM,
        };

        // ── Parse CSV with BOM stripping ─────────────────────────
        $path = $request->file('file')->getRealPath();
        $content = file_get_contents($path);
        $content = str_replace("\u{FEFF}", '', $content); // strip BOM (Excel adds this)
        $content = str_replace("\r\n", "\n", $content);   // normalize Windows line endings
        $content = str_replace("\r", "\n", $content);     // normalize old Mac line endings

        $lines = array_filter(explode("\n", trim($content))); // remove empty lines
        $lines = array_values($lines);

        if (count($lines) < 2) {
            return back()->with('error', 'File is empty or has no data rows.');
        }

        // First line = headers
        $headers = array_map('trim', str_getcsv(array_shift($lines)));

        // Check required headers exist
        $required = ['admission_number', $scoreKey];
        foreach ($required as $col) {
            if (! in_array($col, $headers)) {
                return back()->with('error', "CSV is missing required column: \"{$col}\". Please use the correct template.");
            }
        }

        // Build rows as associative arrays
        $rows = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            $values = str_getcsv($line);
            // Pad values if row has fewer columns than headers
            while (count($values) < count($headers)) {
                $values[] = '';
            }
            $rows[] = array_combine($headers, array_map('trim', $values));
        }

        if (empty($rows)) {
            return back()->with('error', 'No data rows found in the file.');
        }

        // ── Process rows ──────────────────────────────────────────
        $errors = [];
        $imported = 0;

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                $line = $index + 2; // +2 because line 1 = header
                $admissionNo = trim($row['admission_number'] ?? '');

                if (empty($admissionNo)) {
                    continue;
                }

                $student = Student::withoutGlobalScope('school')
                    ->where('school_id', Auth::user()->school_id)
                    ->where('admission_number', $admissionNo)
                    ->first();

                if (! $student) {
                    $errors[] = "Row {$line}: Admission number \"{$admissionNo}\" not found.";

                    continue;
                }

                // Validate score
                $scoreRaw = $row[$scoreKey] ?? '';
                if ($scoreRaw === '' || ! is_numeric($scoreRaw)) {
                    $errors[] = "Row {$line} ({$student->first_name} {$student->last_name}): {$scoreKey} is missing or not a number.";

                    continue;
                }

                $score = (float) $scoreRaw;

                if ($score < 0 || $score > $maxScore) {
                    $errors[] = "Row {$line} ({$student->first_name} {$student->last_name}): {$scoreKey} must be between 0 and {$maxScore}.";

                    continue;
                }

                // Bonus
                $bonus = is_numeric($row['bonus_mark'] ?? '') ? (float) $row['bonus_mark'] : 0;
                $bonusComponent = strtolower(trim($row['bonus_component'] ?? ''));

                if ($bonus < 0) {
                    $errors[] = "Row {$line} ({$student->first_name} {$student->last_name}): Bonus cannot be negative.";

                    continue;
                }

                if ($bonus > 0 && $bonusComponent !== $component) {
                    $errors[] = "Row {$line} ({$student->first_name} {$student->last_name}): bonus_component must match upload component ({$component}).";

                    continue;
                }

                // Apply bonus — cannot push score above max
                if ($bonus > 0) {
                    $score = $score + min($bonus, $maxScore - $score);
                }

                // Get existing result or create blank
                $existing = Result::firstOrNew([
                    'school_id' => Auth::user()->school_id,
                    'student_id' => $student->id,
                    'class_id' => $request->class_id,
                    'subject_id' => $request->subject_id,
                    'session_id' => $currentSession->id,
                    'term_id' => $currentTerm->id,
                ]);

                // Only update the uploaded component
                $existing->$scoreKey = $score;
                $existing->uploaded_by = Auth::id();

                if ($bonus > 0) {
                    $existing->bonus_mark = $bonus;
                    $existing->bonus_component = $component;
                }

                // Recompute total
                $ca1 = (float) ($existing->ca1_score ?? 0);
                $ca2 = (float) ($existing->ca2_score ?? 0);
                $exam = (float) ($existing->exam_score ?? 0);
                $total = min(100, round($ca1 + $ca2 + $exam, 2));

                $existing->total_score = $total;
                $existing->grade = Result::computeGrade($total);
                $existing->remark = Result::computeRemark($existing->grade);

                $existing->save();
                $imported++;
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Upload failed: '.$e->getMessage());
        }

        // ── Redirect with feedback ────────────────────────────────
        $redirectParams = [
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
        ];

        if (count($errors)) {
            return redirect()->route('staff.results.upload.form', $redirectParams)
                ->with('import_errors', $errors)
                ->with('warning', "{$imported} results uploaded. ".count($errors).' rows had errors.');
        }

        return redirect()->route('staff.results.upload.form', $redirectParams)
            ->with('success', "{$imported} ".strtoupper($component).' results uploaded successfully.');
    }
      // ── Edit single student result ────────────────────────────
    public function editResult(Result $result)
    {
        abort_if($result->school_id !== Auth::user()->school_id, 403);
        $result->load(['student', 'schoolClass.level', 'schoolClass.section', 'subject']);

        return view('dashboards.admin.results.edit', compact('result'));
    }

    public function updateResult(Request $request, Result $result)
    {
        abort_if($result->school_id !== Auth::user()->school_id, 403);

        $data = $request->validate([
            'ca1_score' => ['nullable', 'numeric', 'min:0', 'max:'.Result::MAX_CA1],
            'ca2_score' => ['nullable', 'numeric', 'min:0', 'max:'.Result::MAX_CA2],
            'exam_score' => ['nullable', 'numeric', 'min:0', 'max:'.Result::MAX_EXAM],
            'bonus_mark' => ['nullable', 'numeric', 'min:0'],
            'bonus_component' => ['nullable', 'in:ca1,ca2,exam'],
        ]);

        $ca1 = (float) ($data['ca1_score'] ?? $result->ca1_score ?? 0);
        $ca2 = (float) ($data['ca2_score'] ?? $result->ca2_score ?? 0);
        $exam = (float) ($data['exam_score'] ?? $result->exam_score ?? 0);

        // Apply bonus if provided
        $bonus = (float) ($data['bonus_mark'] ?? 0);
        $bonusComponent = $data['bonus_component'] ?? null;

        if ($bonus > 0 && $bonusComponent) {
            $maxMap = ['ca1' => Result::MAX_CA1, 'ca2' => Result::MAX_CA2, 'exam' => Result::MAX_EXAM];
            $space = match ($bonusComponent) {
                'ca1' => $maxMap['ca1'] - $ca1,
                'ca2' => $maxMap['ca2'] - $ca2,
                'exam' => $maxMap['exam'] - $exam,
                default => 0,
            };
            $appliedBonus = min($bonus, $space);
            match ($bonusComponent) {
                'ca1' => $ca1 += $appliedBonus,
                'ca2' => $ca2 += $appliedBonus,
                'exam' => $exam += $appliedBonus,
                default => null,
            };
        }

        $total = min(100, round($ca1 + $ca2 + $exam, 2));

        $result->update([
            'ca1_score' => $ca1,
            'ca2_score' => $ca2,
            'exam_score' => $exam,
            'bonus_mark' => $bonus ?: null,
            'bonus_component' => $bonusComponent,
            'total_score' => $total,
            'grade' => Result::computeGrade($total),
            'remark' => Result::computeRemark(Result::computeGrade($total)),
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()
            ->route('admin.results.view', [
                'class_id' => $result->class_id,
                'subject_id' => $result->subject_id,
            ])
            ->with('success', "Result for {$result->student->full_name} updated successfully.");
    }

    public function downloadTemplate(Request $request)
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'component' => ['required', 'in:ca1,ca2,exam'],
        ]);

        $currentSession = AcademicSession::current()->first();
        $currentTerm = Term::current()->first();

        $class = SchoolClass::with(['level', 'section'])->findOrFail($request->class_id);
        $subject = Subject::findOrFail($request->subject_id);

        $students = Student::whereHas('classAssignments', fn ($q) => $q->where('class_id', $request->class_id)
            ->when($currentSession, fn ($q) => $q->where('session_id', $currentSession->id))
            ->when($currentTerm, fn ($q) => $q->where('term_id', $currentTerm->id))
        )
            ->active()->orderBy('last_name')->get();

        $component = $request->component; // ca1, ca2, exam
        $maxScore = match ($component) {
            'ca1' => Result::MAX_CA1,
            'ca2' => Result::MAX_CA2,
            'exam' => Result::MAX_EXAM,
        };

        $filename = "results_{$component}_"
            .str_replace([' ', '/'], '_', $class->full_name).'_'
            .str_replace(' ', '_', $subject->name).'_'
            .($currentTerm?->name ?? '').'_term.csv';

        $callback = function () use ($students, $component) {
            $file = fopen('php://output', 'w');
            // Header row: admission_number, student_name, the chosen component, bonus_mark, bonus_component
            fputcsv($file, [
                'admission_number',
                'student_name',
                "{$component}_score",   // e.g. ca1_score — max {$maxScore}
                'bonus_mark',           // optional
                'bonus_component',      // same as component above if adding bonus
            ]);
            foreach ($students as $student) {
                fputcsv($file, [
                    $student->admission_number,
                    $student->full_name,
                    '', '', '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ── View Results ──────────────────────────────────────────
    public function viewResult(Request $request)
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
        ]);

        $currentSession = AcademicSession::current()->first();
        $currentTerm = Term::current()->first();

        $class = SchoolClass::with(['level', 'section'])->findOrFail($request->class_id);
        $subject = Subject::findOrFail($request->subject_id);

        $results = Result::with('student')
            ->where('class_id', $request->class_id)
            ->where('subject_id', $request->subject_id)
            ->when($currentTerm, fn ($q) => $q->where('term_id', $currentTerm->id))
            ->orderBy('total_score', 'desc')
            ->get();

        $stats = [
            'highest' => $results->max('total_score') ?? 0,
            'lowest' => $results->min('total_score') ?? 0,
            'average' => round($results->avg('total_score') ?? 0, 1),
            'passed' => $results->whereNotIn('grade', ['E', 'F'])->count(),
            'failed' => $results->whereIn('grade', ['E', 'F'])->count(),
        ];

        return view('dashboards.admin.results.view', compact(
            'class', 'subject', 'results', 'stats', 'currentSession', 'currentTerm'
        ));
    }

    // ── Delete ────────────────────────────────────────────────
    public function destroyResult(Result $result)
    {
        abort_if($result->school_id !== Auth::user()->school_id, 403);
        $result->delete();

        return back()->with('success', 'Result deleted.');
    }

    // ── Parse CSV ─────────────────────────────────────────────
    private function parseCsv(string $path): array
    {
        $rows = [];
        $headers = null;
        $handle = fopen($path, 'r');
        if (! $handle) {
            return [];
        }

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if ($headers === null) {
                $headers = array_map('trim', array_map('strtolower', $row));

                continue;
            }
            while (count($row) < count($headers)) {
                $row[] = '';
            }
            $rows[] = array_combine($headers, array_slice($row, 0, count($headers)));
        }

        fclose($handle);

        return $rows;
    }


    public function indexReportCard()
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
        // ->whereIn('id', $assignedClassIds)
        ->orderBy('name')
        ->get();

    return view('dashboards.admin.report-cards.index', compact(
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

        return view('dashboards.admin.report-cards.preview', $data);
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

    private function authorizeSchoolAccess(User $user): void
    {
        abort_if($user->school_id !== Auth::user()->school_id, 403, 'Unauthorized.');
    }
}

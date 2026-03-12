<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolResultController extends Controller
{
    //
    // ── Index: pick class + subject ───────────────────────────
    public function index()
    {
        $currentSession = AcademicSession::current()->first();
        $currentTerm = Term::current()->first();
        $classes = SchoolClass::with(['level', 'section'])->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('dashboards.staff.result.indexing', compact(
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

        return view('dashboards.staff.result.upload', compact(
            'class', 'subject', 'students',
            'existingResults', 'currentSession', 'currentTerm'
        ));
    }

    // ── Download Template ─────────────────────────────────────
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

    public function upload(Request $request)
{
    $request->validate([
        'file'       => 'required|mimetypes:text/csv,text/plain,application/csv,application/vnd.ms-excel',
        'class_id'   => ['required', 'exists:classes,id'],
        'subject_id' => ['required', 'exists:subjects,id'],
        'component'  => ['required', 'in:ca1,ca2,exam'],
    ]);

    $currentSession = AcademicSession::current()->first();
    $currentTerm    = Term::current()->first();

    if (!$currentSession || !$currentTerm) {
        return back()->with('error', 'No active session or term. Contact your admin.');
    }

    $component = $request->component;
    $scoreKey  = "{$component}_score";
    $maxScore  = match ($component) {
        'ca1'  => Result::MAX_CA1,
        'ca2'  => Result::MAX_CA2,
        'exam' => Result::MAX_EXAM,
    };

    // ── Parse CSV with BOM stripping ─────────────────────────
    $path    = $request->file('file')->getRealPath();
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
        if (!in_array($col, $headers)) {
            return back()->with('error', "CSV is missing required column: \"{$col}\". Please use the correct template.");
        }
    }

    // Build rows as associative arrays
    $rows = [];
    foreach ($lines as $line) {
        if (trim($line) === '') continue;
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
    $errors   = [];
    $imported = 0;

    DB::beginTransaction();
    try {
        foreach ($rows as $index => $row) {
            $line        = $index + 2; // +2 because line 1 = header
            $admissionNo = trim($row['admission_number'] ?? '');

            if (empty($admissionNo)) {
                continue;
            }

            $student = Student::withoutGlobalScope('school')
                ->where('school_id', Auth::user()->school_id)
                ->where('admission_number', $admissionNo)
                ->first();

            if (!$student) {
                $errors[] = "Row {$line}: Admission number \"{$admissionNo}\" not found.";
                continue;
            }

            // Validate score
            $scoreRaw = $row[$scoreKey] ?? '';
            if ($scoreRaw === '' || !is_numeric($scoreRaw)) {
                $errors[] = "Row {$line} ({$student->first_name} {$student->last_name}): {$scoreKey} is missing or not a number.";
                continue;
            }

            $score = (float) $scoreRaw;

            if ($score < 0 || $score > $maxScore) {
                $errors[] = "Row {$line} ({$student->first_name} {$student->last_name}): {$scoreKey} must be between 0 and {$maxScore}.";
                continue;
            }

            // Bonus
            $bonus          = is_numeric($row['bonus_mark'] ?? '') ? (float) $row['bonus_mark'] : 0;
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
                'school_id'  => Auth::user()->school_id,
                'student_id' => $student->id,
                'class_id'   => $request->class_id,
                'subject_id' => $request->subject_id,
                'session_id' => $currentSession->id,
                'term_id'    => $currentTerm->id,
            ]);

            // Only update the uploaded component
            $existing->$scoreKey    = $score;
            $existing->uploaded_by  = Auth::id();

            if ($bonus > 0) {
                $existing->bonus_mark      = $bonus;
                $existing->bonus_component = $component;
            }

            // Recompute total
            $ca1   = (float) ($existing->ca1_score  ?? 0);
            $ca2   = (float) ($existing->ca2_score  ?? 0);
            $exam  = (float) ($existing->exam_score ?? 0);
            $total = min(100, round($ca1 + $ca2 + $exam, 2));

            $existing->total_score = $total;
            $existing->grade       = Result::computeGrade($total);
            $existing->remark      = Result::computeRemark($existing->grade);

            $existing->save();
            $imported++;
        }

        DB::commit();

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Upload failed: ' . $e->getMessage());
    }

    // ── Redirect with feedback ────────────────────────────────
    $redirectParams = [
        'class_id'   => $request->class_id,
        'subject_id' => $request->subject_id,
    ];

    if (count($errors)) {
        return redirect()->route('staff.results.upload.form', $redirectParams)
            ->with('import_errors', $errors)
            ->with('warning', "{$imported} results uploaded. " . count($errors) . ' rows had errors.');
    }

    return redirect()->route('staff.results.upload.form', $redirectParams)
        ->with('success', "{$imported} " . strtoupper($component) . ' results uploaded successfully.');
}

    // ── Edit single student result ────────────────────────────
    public function edit(Result $result)
    {
        abort_if($result->school_id !== Auth::user()->school_id, 403);
        $result->load(['student', 'schoolClass.level', 'schoolClass.section', 'subject']);

        return view('dashboards.staff.result.edit', compact('result'));
    }

    public function update(Request $request, Result $result)
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
            ->route('staff.results.view', [
                'class_id' => $result->class_id,
                'subject_id' => $result->subject_id,
            ])
            ->with('success', "Result for {$result->student->full_name} updated successfully.");
    }

    // ── View Results ──────────────────────────────────────────
    public function view(Request $request)
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

        return view('dashboards.staff.result.view', compact(
            'class', 'subject', 'results', 'stats', 'currentSession', 'currentTerm'
        ));
    }

    // ── Delete ────────────────────────────────────────────────
    public function destroy(Result $result)
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
}

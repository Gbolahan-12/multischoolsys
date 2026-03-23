<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\StudentClassAssignment;
use App\Models\AcademicSession;
use App\Models\Term;
use Illuminate\Http\UploadedFile;

class StudentsImport
{
    private int   $schoolId;
    private ?int  $classId;
    private int   $rowCount = 0;
    private array $errors   = [];
    private ?int  $currentSessionId;
    private ?int  $currentTermId;

    public function __construct(int $schoolId, ?int $classId = null)
    {
        $this->schoolId = $schoolId;
        $this->classId  = $classId;

        $session = AcademicSession::current()->first();
        $term    = Term::current()->first();

        $this->currentSessionId = $session?->id;
        $this->currentTermId    = $term?->id;
    }

    public function import(UploadedFile $file): void
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'csv') {
            $this->importCsv($file->getRealPath());
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            $this->importExcel($file->getRealPath(), $extension);
        } else {
            throw new \Exception('Unsupported file type. Please upload .csv, .xlsx or .xls');
        }
    }

    // ── CSV Import ───────────────────────────────────────────
    private function importCsv(string $path): void
    {
        $content = file_get_contents($path);
        $content = str_replace("\u{FEFF}", '', $content);
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);

        $lines = array_filter(explode("\n", trim($content)));
        $lines = array_values($lines);

        if (count($lines) < 2) return;

        $headers = array_map('trim', array_map('strtolower', str_getcsv(array_shift($lines))));

        $line = 1;
        foreach ($lines as $row) {
            $line++;
            if (trim($row) === '') continue;
            $values = str_getcsv($row);
            while (count($values) < count($headers)) $values[] = '';
            $data = array_combine($headers, array_map('trim', $values));
            $this->processRow($data, $line);
        }
    }

    // ── Excel Import ─────────────────────────────────────────
    private function importExcel(string $path, string $extension): void
    {
        if ($extension === 'xlsx') {
            $rows = $this->readXlsx($path);
        } else {
            throw new \Exception('Please save your file as .xlsx or .csv format and try again.');
        }

        $headers = null;
        $line    = 0;

        foreach ($rows as $row) {
            $line++;
            if ($headers === null) {
                $headers = array_map('trim', array_map('strtolower', $row));
                continue;
            }

            while (count($row) < count($headers)) $row[] = '';
            $data = array_combine($headers, array_slice($row, 0, count($headers)));
            $this->processRow($data, $line);
        }
    }

    // ── Read XLSX using ZipArchive + SimpleXML ────────────────
    private function readXlsx(string $path): array
    {
        if (!extension_loaded('zip')) {
            throw new \Exception('ZIP extension not available. Please use CSV format instead.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \Exception('Could not open xlsx file.');
        }

        $sharedStrings = [];
        $ssXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssXml) {
            $ss = simplexml_load_string($ssXml);
            foreach ($ss->si as $si) {
                if (isset($si->t)) {
                    $sharedStrings[] = (string) $si->t;
                } else {
                    $text = '';
                    foreach ($si->r as $r) $text .= (string) $r->t;
                    $sharedStrings[] = $text;
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if (!$sheetXml) {
            throw new \Exception('Could not read sheet data from xlsx file.');
        }

        $sheet = simplexml_load_string($sheetXml);
        $rows  = [];

        foreach ($sheet->sheetData->row as $row) {
            $rowData    = [];
            $lastColIdx = -1;

            foreach ($row->c as $cell) {
                preg_match('/([A-Z]+)/', (string) $cell['r'], $matches);
                $colIdx = $this->colLetterToIndex($matches[1]);

                while ($lastColIdx < $colIdx - 1) {
                    $rowData[] = '';
                    $lastColIdx++;
                }

                $value = '';
                if (isset($cell->v)) {
                    $value = (string) $cell['t'] === 's'
                        ? ($sharedStrings[(int) $cell->v] ?? '')
                        : (string) $cell->v;
                }

                $rowData[]  = $value;
                $lastColIdx = $colIdx;
            }

            if (!empty(array_filter($rowData))) {
                $rows[] = $rowData;
            }
        }

        return $rows;
    }

    private function colLetterToIndex(string $col): int
    {
        $index = 0;
        for ($i = 0; $i < strlen($col); $i++) {
            $index = $index * 26 + (ord($col[$i]) - ord('A') + 1);
        }
        return $index - 1;
    }

    // ── Process Each Row ─────────────────────────────────────
    private function processRow(array $row, int $line): void
    {
        $firstName = trim($row['first_name'] ?? '');
        $lastName  = trim($row['last_name']  ?? '');

        if (empty($firstName) || empty($lastName)) {
            return;
        }

        $gender = strtolower(trim($row['gender'] ?? ''));
        if (!in_array($gender, ['male', 'female'])) {
            $this->errors[] = "Row {$line} \"{$firstName} {$lastName}\": invalid gender \"{$row['gender']}\". Use male or female.";
            return;
        }

        $admissionNumber = !empty($row['admission_number'])
            ? trim($row['admission_number'])
            : Student::generateAdmissionNumber($this->schoolId);

        $exists = Student::withoutGlobalScope('school')
            ->where('school_id', $this->schoolId)
            ->where('admission_number', $admissionNumber)
            ->exists();

        if ($exists) {
            $this->errors[] = "Row {$line} \"{$firstName} {$lastName}\": admission number \"{$admissionNumber}\" already exists. Skipped.";
            return;
        }

        $dob = null;
        if (!empty($row['date_of_birth'])) {
            try {
                $dob = \Carbon\Carbon::parse($row['date_of_birth'])->format('Y-m-d');
            } catch (\Exception $e) {
                $dob = null;
            }
        }

        $student = Student::create([
            'school_id'        => $this->schoolId,
            'admission_number' => $admissionNumber,
            'first_name'       => $firstName,
            'last_name'        => $lastName,
            'other_name'       => trim($row['other_name'] ?? '') ?: null,
            'gender'           => $gender,
            'date_of_birth'    => $dob,
            'guardian_name'    => trim($row['guardian_name']  ?? '') ?: null,
            'guardian_phone'   => trim($row['guardian_phone'] ?? '') ?: null,
            'guardian_email'   => trim($row['guardian_email'] ?? '') ?: null,
            'address'          => trim($row['address']        ?? '') ?: null,
            'is_active'        => true,
        ]);

        // ── Assign to class if class_id was selected ──────────
        if ($this->classId && $this->currentSessionId && $this->currentTermId) {
            $alreadyAssigned = StudentClassAssignment::where('student_id', $student->id)
                ->where('term_id', $this->currentTermId)
                ->exists();

            if (!$alreadyAssigned) {
                StudentClassAssignment::create([
                    'student_id' => $student->id,
                    'class_id'   => $this->classId,
                    'session_id' => $this->currentSessionId,
                    'term_id'    => $this->currentTermId,
                ]);
            }
        } elseif ($this->classId && (!$this->currentSessionId || !$this->currentTermId)) {
            if ($this->rowCount === 0) {
                $this->errors[] = "Warning: No active session/term found — class assignments were skipped for all students.";
            }
        }

        $this->rowCount++;
    }

    public function getRowCount(): int { return $this->rowCount; }
    public function getErrors(): array { return $this->errors; }
}
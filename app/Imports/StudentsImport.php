<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Http\UploadedFile;

class StudentsImport
{
    private int   $schoolId;
    private int   $rowCount = 0;
    private array $errors   = [];

    public function __construct(int $schoolId)
    {
        $this->schoolId = $schoolId;
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
        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new \Exception('Could not open the uploaded file.');
        }

        $headers = null;
        $line    = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $line++;

            // First row = headers
            if ($headers === null) {
                $headers = array_map('trim', array_map('strtolower', $row));
                continue;
            }

            if (count($row) !== count($headers)) continue;

            $data = array_combine($headers, $row);
            $this->processRow($data, $line);
        }

        fclose($handle);
    }

    // ── Excel Import using ZipArchive (xlsx) ─────────────────
    private function importExcel(string $path, string $extension): void
    {
        // For xlsx files, extract the shared strings and sheet data from the zip
        if ($extension === 'xlsx') {
            $rows = $this->readXlsx($path);
        } else {
            // xls is binary — tell user to convert to csv
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

            // Pad row to match header count
            while (count($row) < count($headers)) {
                $row[] = '';
            }

            $data = array_combine($headers, array_slice($row, 0, count($headers)));
            $this->processRow($data, $line);
        }
    }

    // ── Read XLSX using ZipArchive + SimpleXML ───────────────
    private function readXlsx(string $path): array
    {
        if (!extension_loaded('zip')) {
            throw new \Exception('ZIP extension not available. Please use CSV format instead.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \Exception('Could not open xlsx file.');
        }

        // Read shared strings
        $sharedStrings = [];
        $ssXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssXml) {
            $ss = simplexml_load_string($ssXml);
            foreach ($ss->si as $si) {
                // Handle both plain text and rich text
                if (isset($si->t)) {
                    $sharedStrings[] = (string) $si->t;
                } else {
                    $text = '';
                    foreach ($si->r as $r) {
                        $text .= (string) $r->t;
                    }
                    $sharedStrings[] = $text;
                }
            }
        }

        // Read first sheet
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
                // Get column index from cell reference (e.g. "A1" -> 0, "B1" -> 1)
                preg_match('/([A-Z]+)/', (string) $cell['r'], $matches);
                $colIdx = $this->colLetterToIndex($matches[1]);

                // Fill gaps with empty strings
                while ($lastColIdx < $colIdx - 1) {
                    $rowData[] = '';
                    $lastColIdx++;
                }

                $value = '';
                if (isset($cell->v)) {
                    if ((string) $cell['t'] === 's') {
                        // Shared string
                        $value = $sharedStrings[(int) $cell->v] ?? '';
                    } else {
                        $value = (string) $cell->v;
                    }
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
        $index  = 0;
        $length = strlen($col);
        for ($i = 0; $i < $length; $i++) {
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
            return; // silently skip empty rows
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

        // Parse date_of_birth safely
        $dob = null;
        if (!empty($row['date_of_birth'])) {
            try {
                $dob = \Carbon\Carbon::parse($row['date_of_birth'])->format('Y-m-d');
            } catch (\Exception $e) {
                $dob = null;
            }
        }

        Student::create([
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

        $this->rowCount++;
    }

    public function getRowCount(): int { return $this->rowCount; }
    public function getErrors(): array { return $this->errors; }
}
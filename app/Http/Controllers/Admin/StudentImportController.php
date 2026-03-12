<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentImportController extends Controller
{
    //
    public function showForm()
    {
        return view('dashboards.admin.student.uploadstudents');
    }

    public function import(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle);

        // Remove BOM + trim spaces
        $header = array_map(function ($value) {
            $value = preg_replace('/^\xEF\xBB\xBF/', '', $value); // remove BOM

            return trim(strtolower($value));
        }, $header);

        // dd($header);

        while (($row = fgetcsv($handle)) !== false) {

            $data = array_combine($header, $row);

            if (empty($data['fullname']) || empty($data['email'])) {
                continue;
            }

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'fullname' => $data['fullname'],
                    'role' => 'student',
                    'school_id' => $data['school_id'] ?? null,
                    'class_id' => $data['class_id'] ?? null,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'school_id' => $data['school_id'] ?? null,
                    'class_id' => $data['class_id'] ?? null,
                    'dob' => $this->formatDate($data['dob'] ?? null),
                    'gender' => strtolower($data['gender'] ?? null),
                    'parent_phone_no' => $data['parent_phone_no'] ?? null,
                    'admission_no' => Student::generateAdmissionNo($data['school_id']),
                ]
            );

        }

        fclose($handle);

        return back()->with('success', 'Students imported successfully!');
    }

    public function importExcelForm()
    {
        return view('dashboards.admin.student.excel');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');

        $spreadsheet = IOFactory::load($file->getPathname());
        
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        // dd($rows);
        
       unset($rows[0]);
        
        foreach ($rows as $row) {

            if (!$row[0]) {
                continue;
            }
            dd($row[4]);

            $admissionNo = Student::generateAdmissionNo($row[5]);

            Student::create([
                'fullname' => $row[0],
                'email' => $row[1],
                'school_id' => $row[2],
                'class_id' => $row[3],
                'dob' => \Carbon\Carbon::parse($row[4])->format('Y-m-d'),
                'gender' => strtolower($row[5]),
                'parent_phone_no' => $row[6],
                'admission_no' => $admissionNo,
            ]);
        }

        return back()->with('success', 'Students imported successfully!');
    }

    private function formatDate($date)
    {
        if (! $date) {
            return null;
        }

        try {
            // Handles: 5/1/2010, 05/01/2010
            return \Carbon\Carbon::createFromFormat('n/j/Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                // Handles: 05/01/2010
                return \Carbon\Carbon::createFromFormat('m/d/Y', $date)->format('Y-m-d');
            } catch (\Exception $e) {
                return null; // invalid date → saved as NULL
            }
        }
    }
}

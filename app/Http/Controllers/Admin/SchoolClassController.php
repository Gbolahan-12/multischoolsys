<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SchoolClassController extends Controller
{
    //
    public function classlistView()
    {
        $schools = School::all();
        $schclasses = SchoolClass::with('school','students')->paginate(10);

        return view('dashboards.admin.class.classlist', compact('schools', 'schclasses'));
    }

    public function schoolListView()
    {
        $schools = School::get();

        return view('dashboards.admin.school.schoollist', compact('schools'));
    }

    public function schoolStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:schools,name',
            'address' => 'required|max:255|unique:schools,address',
            'type' => 'required|in:primary,secondary',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal', 'createSchool');
        }

        School::create($validator->validated());

        return redirect()
            ->back()
            ->with('success', 'School Created Successfully!');
    }

    public function storeClass(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal', 'createClass');
        }

        $school = School::findOrFail($request->school_id);
        $school->classes()->create($validator->validated());

        return redirect()
            ->back()
            ->with('success', 'Class Created Successfully!');

    }

    public function studentList()
    {
        $schools = School::all();
        $students = User::with(['school', 'class', 'student'])
            ->where('role', 'student')
            ->latest()
            ->get();

        return view(
            'dashboards.admin.student.studentlist',
            compact('students', 'schools')
        );
    }

    public function classesBySchool(School $school)
    {
        return response()->json(
            $school->classes()
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
        );
    }

    public function storeStudent(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'fullname' => 'required',
            'email' => 'nullable|email',
            'school_id' => 'required|exists:schools,id',
            'class_id' => 'required|exists:classes,id',
            'dob' => 'nullable|date',
            'parent_phone_no' => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($validated) {

        $user = User::create([
            'fullname' => $validated['fullname'],
            'email' => $validated['email'],
            'school_id' => $validated['school_id'],
            'class_id' => $validated['class_id'],
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);
        $admissionNo = Student::generateAdmissionNo($validated['school_id']);

        Student::create([
            'user_id' => $user->id,
            'school_id' => $validated['school_id'],
            'class_id' => $validated['class_id'],
            'admission_no' => $admissionNo,
            'dob' => $validated['dob'] ?? null,
            'parent_phone_no' => $validated['parent_phone_no'] ?? null,
        ]);

        });

        return back()->with('success', 'Student created successfully');
    }

    public function classFees()
    {
        $schools = School::all();
        $fees = Fee::with('school')->get();

        return view('dashboards.admin.class.classfee', compact('schools', 'fees'));
    }

    public function storeClassFee(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'school_id' => 'required|exists:schools,id',
            'class_id' => 'required|exists:classes,id',
            'session' => 'required|string|max:9',
            'type' => 'required|in:compulsory,defaulter',
            'term' => 'required|in:first,second,third',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal', 'setClassFee');
        }

        $school = School::findOrFail($request->school_id);
        $class = SchoolClass::findOrFail($request->class_id);

        $class->fees()->updateOrCreate(
            [
                'session' => $request->session,
                'term' => $request->term,
            ],
            [
                'school_id' => $request->school_id,
                'title' => $request->title,
                'amount' => $request->amount,
                'type' => $request->type,
            ]
        );

        return back()->with('success', 'Class Fee Set Successfully!');
    }

    public function assignClass()
    {
        return view('dashboards.admin.class.assignclass');
    }
}

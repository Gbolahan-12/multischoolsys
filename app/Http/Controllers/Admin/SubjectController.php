<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::withCount('classAssignments')->orderBy('name')->get();
        return view('dashboards.admin.subjects.index', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('subjects')->where('school_id', Auth::user()->school_id),
            ],
            'code' => ['nullable', 'string', 'max:20'],
        ]);

        Subject::create([
            'school_id' => Auth::user()->school_id,
            'name'      => $request->name,
            'code'      => $request->code,
        ]);

        return back()->with('success', "Subject \"{$request->name}\" created successfully.");
    }

    public function update(Request $request, Subject $subject)
    {
        $this->authorizeSchool($subject);

        $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('subjects')
                    ->where('school_id', Auth::user()->school_id)
                    ->ignore($subject->id),
            ],
            'code' => ['nullable', 'string', 'max:20'],
        ]);

        $subject->update(['name' => $request->name, 'code' => $request->code]);
        return back()->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        $this->authorizeSchool($subject);

        if ($subject->classAssignments()->exists()) {
            return back()->with('error', 'Cannot delete a subject that is assigned to classes.');
        }

        $subject->delete();
        return back()->with('success', 'Subject deleted.');
    }

    private function authorizeSchool(Subject $subject): void
    {
        abort_if($subject->school_id !== Auth::user()->school_id, 403);
    }
}
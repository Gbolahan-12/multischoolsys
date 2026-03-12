<?php

namespace App\Http\Controllers;

use App\Models\SingleResultUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SingleResultUploadController extends Controller
{
    //
    public function storeResult(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'school_id' => 'required|exists:schools,id',
            'assessment_type' => 'required|string|max:50',
            'score' => 'required|numeric|min:0|max:100',
            'term' => 'required|string|max:20',
            'session' => 'required|string|max:20',
        ]);
        $exists = SingleResultUpload::where([
            'student_id' => $request->student_id,
            'subject_id' => $request->subject_id,
            'term' => $request->term,
            'session' => $request->session,
        ])->exists();

        if ($exists) {
            return back()->withErrors('Result already uploaded for this subject');
        }

        $validated['uploaded_by'] = Auth::id();

        SingleResultUpload::create($validated);

        return back()->with('success', 'Result uploaded successfully');
    }

    public function editResult($id)
    {
        $result = SingleResultUpload::findOrFail($id);

        return view('results.edit', compact('result'));
    }

    public function updateResult(Request $request, $id)
    {
        $result = SingleResultUpload::findOrFail($id);

        $validated = $request->validate([
            'assessment_type' => 'required|string|max:50',
            'score' => 'required|numeric|min:0|max:100',
            'term' => 'required|string|max:20',
            'session' => 'required|string|max:20',
        ]);

        $result->update($validated);

        return redirect()->route('results.index')
            ->with('success', 'Result updated successfully');
    }

    public function getStudentResultsByTerm($studentId, $term, $session)
    {
        $results = SingleResultUpload::with('subject')
            ->where('student_id', $studentId)
            ->where('term', $term)
            ->where('session', $session)
            ->orderBy('subject_id')
            ->get();

        return view('results.student_term_results', compact('results'));
    }
}

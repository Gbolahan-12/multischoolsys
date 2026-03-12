<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassLevel;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClassSettingsController extends Controller
{
    public function index()
    {
        $levels   = ClassLevel::withCount('classes')->orderBy('name')->get();
        $sections = ClassSection::withCount('classes')->orderBy('name')->get();

        return view('dashboards.admin.classes.settings', compact('levels', 'sections'));
    }

    // ── Levels ──────────────────────────────────────────────

    public function storeLevel(Request $request)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('class_levels')->where('school_id', Auth::user()->school_id),
            ],
        ]);

        ClassLevel::create([
            'school_id' => Auth::user()->school_id,
            'name'      => $request->name,
        ]);

        return back()->with('success', "Level \"{$request->name}\" created.");
    }

    public function updateLevel(Request $request, ClassLevel $level)
    {
        $this->authorizeSchool($level);

        $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('class_levels')
                    ->where('school_id', Auth::user()->school_id)
                    ->ignore($level->id),
            ],
        ]);

        $level->update(['name' => $request->name]);
        return back()->with('success', 'Level updated.');
    }

    public function destroyLevel(ClassLevel $level)
    {
        $this->authorizeSchool($level);

        if ($level->classes()->exists()) {
            return back()->with('error', 'Cannot delete a level that has classes assigned to it.');
        }

        $level->delete();
        return back()->with('success', 'Level deleted.');
    }

    // ── Sections ────────────────────────────────────────────

    public function storeSection(Request $request)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:20',
                Rule::unique('class_sections')->where('school_id', Auth::user()->school_id),
            ],
        ]);

        ClassSection::create([
            'school_id' => Auth::user()->school_id,
            'name'      => $request->name,
        ]);

        return back()->with('success', "Section \"{$request->name}\" created.");
    }

    public function updateSection(Request $request, ClassSection $section)
    {
        $this->authorizeSchool($section);

        $request->validate([
            'name' => [
                'required', 'string', 'max:20',
                Rule::unique('class_sections')
                    ->where('school_id', Auth::user()->school_id)
                    ->ignore($section->id),
            ],
        ]);

        $section->update(['name' => $request->name]);
        return back()->with('success', 'Section updated.');
    }

    public function destroySection(ClassSection $section)
    {
        $this->authorizeSchool($section);

        if ($section->classes()->exists()) {
            return back()->with('error', 'Cannot delete a section that has classes assigned to it.');
        }

        $section->delete();
        return back()->with('success', 'Section deleted.');
    }

    private function authorizeSchool($model): void
    {
        abort_if($model->school_id !== Auth::user()->school_id, 403);
    }
}
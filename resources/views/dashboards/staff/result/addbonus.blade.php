@extends('layouts.staff')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-2xl font-bold mb-6">Add Bonus Marks</h2>

    <form action="{{ route('results.bonus') }}" method="POST">
        @csrf

        {{-- School --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">School</label>
            <select name="school_id" class="w-full border p-2 rounded" required>
                <option value="">Select School</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}">
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Class --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Class</label>
            <select name="class_id" class="w-full border p-2 rounded" required>
                <option value="">Select Class</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Subject --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Subject</label>
            <select name="subject_id" class="w-full border p-2 rounded" required>
                <option value="">Select Subject</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Term --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Term</label>
            <select name="term" class="w-full border p-2 rounded" required>
                <option value="">Select Term</option>
                <option value="First Term">First Term</option>
                <option value="Second Term">Second Term</option>
                <option value="Third Term">Third Term</option>
            </select>
        </div>

        {{-- Session --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Session</label>
            <input type="text" name="session"
                   placeholder="e.g 2025/2026"
                   class="w-full border p-2 rounded"
                   required>
        </div>

        {{-- Score Type --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Score Type</label>
            <select name="field" class="w-full border p-2 rounded" required>
                <option value="">Select Score Type</option>
                <option value="ca1">CA1 (Max 20)</option>
                <option value="ca2">CA2 (Max 20)</option>
                <option value="exam">Exam (Max 60)</option>
            </select>
        </div>

        {{-- Marks --}}
        <div class="mb-6">
            <label class="block font-medium mb-1">Marks To Add</label>
            <input type="number"
                   name="marks"
                   min="1"
                   class="w-full border p-2 rounded"
                   required>
        </div>

        <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Add Bonus Marks
        </button>

    </form>
</div>
@endsection
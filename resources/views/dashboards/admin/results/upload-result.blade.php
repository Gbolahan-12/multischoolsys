@extends('layouts.admin')

@section('content')


    <div class="row d-flex align-center justify-content-center">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
    <div class="card-body">
        <h4 class="card-title">Upload Result</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('results.store') }}">
            @csrf
            <div class="mb-3">
                <label for="school">School</label>
                <select name="school_id" id="schoolSelect" class="form-control" required>
                    <option value="">Select School</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}"
                            {{ old('school_id') == $school->id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Class --}}
            <div class="mb-3">
                <label for="class">Class</label>
                <select name="class_id" id="classSelect" class="form-control" required
                        {{ old('class_id') ? '' : 'disabled' }}>
                    <option value="">Select Class</option>
                    @if(old('class_id') && old('school_id'))
                        @php
                            $oldClasses = \App\Models\School::find(old('school_id'))->classes;
                        @endphp
                        @foreach($oldClasses as $cls)
                            <option value="{{ $cls->id }}"
                                {{ old('class_id') == $cls->id ? 'selected' : '' }}>
                                {{ $cls->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            {{-- Subject --}}
            <div class="mb-3">
                <label for="subject">Subject</label>
                <select name="subject_id" id="subjectSelect" class="form-control" required
                        {{ old('subject_id') ? '' : 'disabled' }}>
                    <option value="">Select Subject</option>
                    @if(old('subject_id') && old('school_id'))
                        @php
                            $oldSubjects = \App\Models\School::find(old('school_id'))->subjects;
                        @endphp
                        @foreach($oldSubjects as $sub)
                            <option value="{{ $sub->id }}"
                                {{ old('subject_id') == $sub->id ? 'selected' : '' }}>
                                {{ $sub->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            

            <div class="mb-3">
                <label for="term">Term</label>
                <select name="term" id="schoolSelect" class="form-control" required>
                    <option value="first">First Term</option>
                    <option value="second">Second Term</option>
                    <option value="third">Third Term</option>
                    
                </select>
            </div>
            <div class="mb-3">
    <label>Session</label>
    <select name="session" id="sessionSelect" class="form-control">
    </select>
</div>

            {{-- Assessment type --}}
            <div class="mb-3">
                <label for="assessment_type">Assessment Type</label>
                <select name="type" id="assessmentTypeSelect" class="form-control" required>
                    <option value="ca1">CA1</option>
                    <option value="ca2">CA2</option>
                    <option value="exam">Exam</option>
                    
                </select>
            </div>

            {{-- Student --}}
            <div class="mb-3">
                <label for="student">Student</label>
                <select name="student_id" id="studentSelect" class="form-control" required
                        {{ old('student_id') ? '' : 'disabled' }}>
                    <option value="">Select Student</option>
                    {{-- Prefill students if old input exists --}}
                    @if(old('student_id') && old('class_id'))
                        @php
                            $oldStudents = \App\Models\SchoolClass::find(old('class_id'))->students;
                        @endphp
                        @foreach($oldStudents as $stu)
                            <option value="{{ $stu->id }}"
                                {{ old('student_id') == $stu->id ? 'selected' : '' }}>
                                {{ $stu->fullname }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            {{-- Exam --}}
            <div class="mb-3">
                <label for="score">Score</label>
                <input type="number" name="score" class="form-control" min="0" max="60"
                       value="{{ old('score') }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Upload Result</button>
        </form>
    </div>
</div>

{{-- Keep modal open if there are validation errors --}}
        @if ($errors->any())
            <script>
                window.addEventListener('load', function () {
                    const modal = document.getElementById('uploadResultModal'); // your modal id
                    if(modal){
                        new bootstrap.Modal(modal).show();
                    }
                });
            </script>
        @endif



        </div>
    </div>
@endsection
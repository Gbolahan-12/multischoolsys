@extends('layouts.admin')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Create Subject</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('subjects.store') }}" method="POST">
                @csrf

                {{-- Subject Name --}}
                <div class="mb-3">
                    <label>Subject Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                    @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                {{-- School --}}
                <div class="mb-3">
                    <label>Select School</label>
                    <select name="school_id" id="schoolSelect" class="form-control">
                        <option value="">Select School</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" 
                                {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('school_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Classes (Loaded dynamically) --}}
                <div class="mb-3">
                    <label>Assign to Class(es)</label>
                    <div id="classContainer">
                        <p class="text-muted">Select school first</p>
                    </div>
                    @error('classes')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-primary">
                    Create Subject
                </button>

            </form>

        </div>
    </div>
</div>

@endsection
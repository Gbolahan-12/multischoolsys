@extends('layouts.admin')
@section('title', 'Add Student')
@section('content')
<div class="container-fluid px-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Add Student</h4>
            <small class="text-muted">Create a new student record</small>
        </div>
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <form action="{{ route('admin.students.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-12 col-lg-8">

                {{-- Personal Info --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                            <i class="bi bi-person me-2 text-primary"></i>Personal Information
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}"
                                       class="form-control @error('first_name') is-invalid @enderror" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}"
                                       class="form-control @error('last_name') is-invalid @enderror" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">Other Name</label>
                                <input type="text" name="other_name" value="{{ old('other_name') }}"
                                       class="form-control">
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                    <option value="">-- Select --</option>
                                    <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">Date of Birth</label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                       class="form-control">
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">Admission Number</label>
                                <input type="text" name="admission_number" value="{{ old('admission_number') }}"
                                       class="form-control @error('admission_number') is-invalid @enderror"
                                       placeholder="Auto-generated if empty">
                                @error('admission_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Address</label>
                                <input type="text" name="address" value="{{ old('address') }}"
                                       class="form-control" placeholder="Street, City">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Guardian Info --}}
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                            <i class="bi bi-people me-2 text-primary"></i>Guardian Information
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Guardian Name <span class="text-danger">*</span></label>
                                <input type="text" name="guardian_name" value="{{ old('guardian_name') }}"
                                       class="form-control @error('guardian_name') is-invalid @enderror" required>
                                @error('guardian_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Guardian Phone <span class="text-danger">*</span></label>
                                <input type="text" name="guardian_phone" value="{{ old('guardian_phone') }}"
                                       class="form-control @error('guardian_phone') is-invalid @enderror"
                                       placeholder="08012345678" required>
                                @error('guardian_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Guardian Email</label>
                                <input type="email" name="guardian_email" value="{{ old('guardian_email') }}"
                                       class="form-control" placeholder="optional">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Column --}}
            <div class="col-12 col-lg-4">

                {{-- Class Assignment --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                            <i class="bi bi-building me-2 text-primary"></i>Class Assignment
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        @if($session && $term)
                        <div class="alert alert-info py-2 px-3 mb-3" style="font-size:12px;">
                            <i class="bi bi-info-circle me-1"></i>
                            Assigning for <strong>{{ $session->name }}</strong> — {{ ucfirst($term->name) }} Term
                        </div>
                        <select name="class_id" class="form-select">
                            <option value="">-- No Class Yet --</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->full_name }}
                            </option>
                            @endforeach
                        </select>
                        @else
                        <div class="text-muted" style="font-size:13px;">
                            <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                            No active session/term. You can assign a class later.
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Save --}}
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-check me-1"></i> Save Student
                        </button>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection
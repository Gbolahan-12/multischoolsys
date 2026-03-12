@extends('layouts.admin')
@section('title', 'Edit Student')
@section('content')
<div class="container-fluid px-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Edit Student</h4>
            <small class="text-muted">Updating — <strong>{{ $student->full_name }}</strong></small>
        </div>
        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <form action="{{ route('admin.students.update', $student) }}" method="POST">
        @csrf @method('PUT')
        <div class="row g-4">
            <div class="col-12 col-lg-8">

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
                                <input type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}"
                                       class="form-control @error('first_name') is-invalid @enderror" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}"
                                       class="form-control @error('last_name') is-invalid @enderror" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">Other Name</label>
                                <input type="text" name="other_name" value="{{ old('other_name', $student->other_name) }}"
                                       class="form-control">
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                    <option value="male"   {{ old('gender', $student->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">Date of Birth</label>
                                <input type="date" name="date_of_birth"
                                       value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
                                       class="form-control">
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">Admission Number <span class="text-danger">*</span></label>
                                <input type="text" name="admission_number"
                                       value="{{ old('admission_number', $student->admission_number) }}"
                                       class="form-control @error('admission_number') is-invalid @enderror" required>
                                @error('admission_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Address</label>
                                <input type="text" name="address" value="{{ old('address', $student->address) }}"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

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
                                <input type="text" name="guardian_name"
                                       value="{{ old('guardian_name', $student->guardian_name) }}"
                                       class="form-control @error('guardian_name') is-invalid @enderror" required>
                                @error('guardian_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Guardian Phone <span class="text-danger">*</span></label>
                                <input type="text" name="guardian_phone"
                                       value="{{ old('guardian_phone', $student->guardian_phone) }}"
                                       class="form-control @error('guardian_phone') is-invalid @enderror" required>
                                @error('guardian_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Guardian Email</label>
                                <input type="email" name="guardian_email"
                                       value="{{ old('guardian_email', $student->guardian_email) }}"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-floppy me-1"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
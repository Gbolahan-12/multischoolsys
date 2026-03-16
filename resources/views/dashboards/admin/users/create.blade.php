@extends('layouts.admin')

@section('title', 'Create Account')

@section('content')
<div class="container-fluid px-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">Create Account</h4>
            <small class="text-muted">Add a new admin or staff member</small>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="row g-4">

            {{-- Left Column --}}
            <div class="col-12 col-lg-8">

                {{-- Account Info --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                            <i class="bi bi-person-badge me-2 text-primary"></i>Account Information
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="fullname" value="{{ old('fullname') }}"
                                       class="form-control @error('fullname') is-invalid @enderror"
                                       placeholder="e.g. Amara Okafor">
                                @error('fullname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="staff@school.com">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror">
                                    <option value="">-- Select Role --</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff / Teacher</option>
                                </select>
                                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Profile Info --}}
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                            <i class="bi bi-card-list me-2 text-primary"></i>Profile Information
                            <span class="fw-normal text-muted">(optional)</span>
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                       class="form-control" placeholder="e.g. 08012345678">
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">-- Select --</option>
                                    <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other"  {{ old('gender') === 'other'  ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Date of Birth</label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                       class="form-control">
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Employment Date</label>
                                <input type="date" name="employment_date" value="{{ old('employment_date') }}"
                                       class="form-control">
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Qualification</label>
                                <input type="text" name="qualification" value="{{ old('qualification') }}"
                                       class="form-control" placeholder="e.g. B.Ed Mathematics">
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Address</label>
                                <input type="text" name="address" value="{{ old('address') }}"
                                       class="form-control" placeholder="Street, City">
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Column --}}
            <div class="col-12 col-lg-4">

                {{-- Password Notice --}}
                <div class="card border-0 bg-primary bg-opacity-10 rounded-3 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex gap-2">
                            <i class="bi bi-info-circle-fill text-primary fs-5 mt-1 flex-shrink-0"></i>
                            <div>
                                <p class="fw-semibold mb-1 text-primary" style="font-size:14px;">Temporary Password</p>
                                <p class="text-muted mb-0" style="font-size:13px;">
                                    Register user with valid email and inform them to login with default password as <strong>"password".</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-check-fill me-1"></i> Create Account
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection
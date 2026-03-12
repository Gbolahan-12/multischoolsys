@extends('layouts.admin')

@section('title', 'Edit Account')

@section('content')
<div class="container-fluid px-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">Edit Account</h4>
            <small class="text-muted">Updating profile for <strong>{{ $user->fullname }}</strong></small>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf @method('PUT')
        <div class="row g-4 d-flex justify-content-center align-center">

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
                                <input type="text" name="fullname" value="{{ old('name', $user->fullname) }}"
                                       class="form-control @error('fullname') is-invalid @enderror">
                                @error('fullname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="form-control @error('email') is-invalid @enderror">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror">
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff / Teacher</option>
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
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Phone Number</label>
                                <input type="text" name="phone"
                                       value="{{ old('phone', $user->profile?->phone) }}"
                                       class="form-control" placeholder="e.g. 08012345678">
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">-- Select --</option>
                                    <option value="male"   {{ old('gender', $user->profile?->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $user->profile?->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other"  {{ old('gender', $user->profile?->gender) === 'other'  ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Date of Birth</label>
                                <input type="date" name="date_of_birth"
                                       value="{{ old('date_of_birth', $user->profile?->date_of_birth?->format('Y-m-d')) }}"
                                       class="form-control">
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Employment Date</label>
                                <input type="date" name="employment_date"
                                       value="{{ old('employment_date', $user->profile?->employment_date?->format('Y-m-d')) }}"
                                       class="form-control">
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Qualification</label>
                                <input type="text" name="qualification"
                                       value="{{ old('qualification', $user->profile?->qualification) }}"
                                       class="form-control" placeholder="e.g. B.Ed Mathematics">
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Address</label>
                                <input type="text" name="address"
                                       value="{{ old('address', $user->profile?->address) }}"
                                       class="form-control" placeholder="Street, City">
                            </div>

                        </div>
                    </div>
                </div>
                
            </div>
            <div class="submit-bn d-flex align-center justify-content-center"> <button class="btn btn-primary border-0">Edit {{ $user->fullname }}</button> </div>
        </div>
    </form>

</div>
@endsection
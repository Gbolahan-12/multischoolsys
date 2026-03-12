@extends('layouts.' . match(auth()->user()->role) {
    'super-admin' => 'superadmin',
    'proprietor'  => 'proprietor',
    'admin'       => 'admin',
    default       => 'staff',
})

@section('title', 'My Profile')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">My Profile</h4>
            <small class="text-muted">Update your personal information</small>
        </div>
    </div>
    @if($errors->any())
    <div class="alert alert-danger rounded-3">
        @foreach($errors->all() as $error)
            <div><i class="feather-alert-circle me-2"></i>{{ $error }}</div>
        @endforeach
    </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="row g-4">
            <div class="col-12 col-lg-3">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 text-center">
                        <div class="position-relative d-inline-block mb-3">
                            <img id="avatarPreview"
                                 src="{{ asset($profile?->avatar ?? 'profile-images/default-avatar.jpg') }}"
                                 alt="Profile Photo"
                                 class="rounded-circle object-fit-cover border"
                                 style="width:120px;height:120px;object-fit:cover;">
                            <label for="avatarInput"
                                   class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle p-1"
                                   style="width:32px;height:32px;cursor:pointer;"
                                   title="Change photo">
                                <i data-feather="camera" style="font-size:14px;"></i>
                            </label>
                            <input type="file" id="avatarInput" name="avatar"
                                   accept="image/jpg,image/jpeg,image/png,image/webp"
                                   class="d-none">
                        </div>

                        <div class="fw-semibold" style="font-size:15px;">{{ $user->fullname }}</div>
                        <div class="text-muted" style="font-size:13px;">{{ ucfirst(str_replace('-', ' ', $user->role)) }}</div>

                        @if($user->school)
                        <div class="mt-1">
                            <span class="badge bg-primary-subtle text-primary rounded-pill" style="font-size:11px;">
                                {{ $user->school->name }}
                            </span>
                        </div>
                        @endif
                        <small class="text-muted d-block mt-3" style="font-size:11px;">
                            JPG, PNG or WEBP · Max 2MB
                        </small>
                    </div>
                </div>
            </div>

            {{-- ── Right: Form Fields ── --}}
            <div class="col-12 col-lg-9">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold" style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                            Personal Information
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">

                            {{-- Name --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" style="font-size:13px;">
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="fullname"
                                       class="form-control @error('fullname') is-invalid @enderror"
                                       value="{{ old('fullname', $user->fullname) }}" required>
                                @error('fullname')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" style="font-size:13px;">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" style="font-size:13px;">Phone</label>
                                <input type="text" name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $profile?->phone) }}"
                                       placeholder="e.g. 08012345678">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Gender --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" style="font-size:13px;">Gender</label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                    <option value="">Select gender</option>
                                    @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ old('gender', $profile?->gender) === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Date of Birth --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" style="font-size:13px;">Date of Birth</label>
                                <input type="date" name="date_of_birth"
                                       class="form-control @error('date_of_birth') is-invalid @enderror"
                                       value="{{ old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d')) }}">
                                @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Address --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold" style="font-size:13px;">Address</label>
                                <textarea name="address" rows="2"
                                          class="form-control @error('address') is-invalid @enderror"
                                          placeholder="Your address...">{{ old('address', $profile?->address) }}</textarea>
                                @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── Change Password ── --}}
                {{-- <div class="card border-0 shadow-sm rounded-3 mt-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold" style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                            Change Password <span class="text-muted fw-normal">(leave blank to keep current)</span>
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size:13px;">Current Password</label>
                                <input type="password" name="current_password"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       placeholder="Enter current password">
                                @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size:13px;">New Password</label>
                                <input type="password" name="new_password"
                                       class="form-control @error('new_password') is-invalid @enderror"
                                       placeholder="Min 8 characters">
                                @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size:13px;">Confirm New Password</label>
                                <input type="password" name="new_password_confirmation"
                                       class="form-control"
                                       placeholder="Repeat new password">
                            </div>

                        </div>
                    </div>
                </div> --}}

                {{-- Submit --}}
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="feather-save me-2"></i>Save Changes
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>

{{-- Avatar live preview script --}}
<script>
document.getElementById('avatarInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    // Validate size (2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('Image is too large. Maximum size is 2MB.');
        this.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('avatarPreview').src = e.target.result;
    };
    reader.readAsDataURL(file);
});
</script>
@endsection
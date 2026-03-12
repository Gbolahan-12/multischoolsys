@extends('layouts.proprietor')
@section('title', 'School Profile')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">School Profile</h4>
            <small class="text-muted">Update your school's information</small>
        </div>
    </div>


    @if($errors->any())
    <div class="alert alert-danger rounded-3">
        @foreach($errors->all() as $error)
            <div><i class="feather-alert-circle me-2"></i>{{ $error }}</div>
        @endforeach
    </div>
    @endif

    <form action="{{ route('proprietor.school-profile.update') }}"
          method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="row g-4">

            <div class="col-12 col-lg-3">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 text-center">

                        <p class="fw-semibold mb-3" style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                            School Logo
                        </p>
                        <div class="position-relative d-inline-block mb-3">
                            @if($school->logo)
                                <img id="logoPreview"
                                     src="{{ asset($school->logo) }}"
                                     alt="School Logo"
                                     class="rounded-3 border"
                                     style="width:130px;height:130px;object-fit:contain;">
                            @else
                                <div id="logoPlaceholder"
                                     class="rounded-3 border bg-light d-flex align-items-center justify-content-center"
                                     style="width:130px;height:130px;">
                                    <i class="feather-image text-muted" style="font-size:2.5rem;opacity:.4;"></i>
                                </div>
                                <img id="logoPreview"
                                     src=""
                                     alt="School Logo"
                                     class="rounded-3 border d-none"
                                     style="width:130px;height:130px;object-fit:contain;">
                            @endif

                            {{-- Camera overlay --}}
                            <label for="logoInput"
                                   class="position-absolute bottom-0 end-0 btn btn-sm text-white bg-primary rounded-circle p-1"
                                   style="width:32px;height:32px;cursor:pointer;" title="Change logo">
                  <i data-feather="camera" style="font-size:14px;"></i>
                            </label>
                            <input type="file" id="logoInput" name="logo"
                                   accept="image/jpg,image/jpeg,image/png,image/webp"
                                   class="d-none">
                        </div>

                        <small class="text-muted d-block" style="font-size:11px;">
                            JPG, PNG or WEBP · Max 2MB<br>Recommended: 300×300px
                        </small>

                        @if($school->logo)
                        <div class="mt-3">
                            <div class="form-check d-inline-flex align-items-center gap-2">
                                <input type="checkbox" class="form-check-input"
                                       name="remove_logo" value="1" id="removeLogo">
                                <label class="form-check-label text-danger"
                                       style="font-size:12px;" for="removeLogo">
                                    Remove logo
                                </label>
                            </div>
                        </div>
                        @endif

                        <div class="mt-3">
                            {!! $school->status_badge !!}
                        </div>

                        @if($school->activated_at)
                        <small class="text-muted d-block mt-1" style="font-size:11px;">
                            Active since {{ $school->activated_at->format('d M Y') }}
                        </small>
                        @endif

                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-9">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-semibold"
                            style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                            School Information
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" style="font-size:13px;">
                                    School Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $school->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" style="font-size:13px;">
                                    School Email
                                </label>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $school->email) }}"
                                       placeholder="school@example.com">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" style="font-size:13px;">
                                    Phone Number
                                </label>
                                <input type="text" name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $school->phone) }}"
                                       placeholder="e.g. 08012345678">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" style="font-size:13px;">
                                    School Motto
                                </label>
                                <input type="text" name="motto"
                                       class="form-control @error('motto') is-invalid @enderror"
                                       value="{{ old('motto', $school->motto) }}"
                                       placeholder="e.g. Excellence in Education">
                                @error('motto')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" style="font-size:13px;">
                                    School Address
                                </label>
                                <textarea name="address" rows="3"
                                          class="form-control @error('address') is-invalid @enderror"
                                          placeholder="Full school address...">{{ old('address', $school->address) }}</textarea>
                                @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="rounded-3 bg-light p-3" style="font-size:13px;">
                                    <div class="row g-2">
                                        <div class="col-6 col-md-3">
                                            <span class="text-muted d-block">School Slug</span>
                                            <span class="fw-semibold">{{ $school->slug }}</span>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <span class="text-muted d-block">Registered</span>
                                            <span class="fw-semibold">{{ $school->created_at->format('d M Y') }}</span>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <span class="text-muted d-block">Status</span>
                                            {!! $school->status_badge !!}
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <span class="text-muted d-block">Activated</span>
                                            <span class="fw-semibold">
                                                {{ $school->activated_at?->format('d M Y') ?? '—' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="feather-save me-2"></i>Save Changes
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
// Live logo preview
document.getElementById('logoInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    if (file.size > 2 * 1024 * 1024) {
        alert('Image too large. Maximum size is 2MB.');
        this.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = e => {
        const preview     = document.getElementById('logoPreview');
        const placeholder = document.getElementById('logoPlaceholder');

        preview.src = e.target.result;
        preview.classList.remove('d-none');

        if (placeholder) placeholder.classList.add('d-none');
    };
    reader.readAsDataURL(file);
});

// If remove_logo is checked, hide the preview
const removeCheckbox = document.getElementById('removeLogo');
if (removeCheckbox) {
    removeCheckbox.addEventListener('change', function () {
        const preview     = document.getElementById('logoPreview');
        const placeholder = document.getElementById('logoPlaceholder');
        if (this.checked) {
            preview.classList.add('d-none');
            if (placeholder) placeholder.classList.remove('d-none');
        } else {
            preview.classList.remove('d-none');
            if (placeholder) placeholder.classList.add('d-none');
        }
    });
}
</script>
@endsection
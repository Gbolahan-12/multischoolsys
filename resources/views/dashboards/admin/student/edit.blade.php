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

    @if($errors->any())
    <div class="alert alert-danger rounded-3 mb-4">
        @foreach($errors->all() as $error)
        <div><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</div>
        @endforeach
    </div>
    @endif

    <form action="{{ route('admin.students.update', $student) }}"
          method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="row g-4">

            {{-- ── Left Column ── --}}
            <div class="col-12 col-lg-8">

                {{-- Personal Information --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-semibold mb-0 text-uppercase text-muted"
                            style="font-size:12px;letter-spacing:.05em;">
                            <i class="bi bi-person me-2 text-primary"></i>Personal Information
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">
                                    First Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="first_name"
                                       value="{{ old('first_name', $student->first_name) }}"
                                       class="form-control @error('first_name') is-invalid @enderror"
                                       required>
                                @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">
                                    Last Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="last_name"
                                       value="{{ old('last_name', $student->last_name) }}"
                                       class="form-control @error('last_name') is-invalid @enderror"
                                       required>
                                @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">Other Name</label>
                                <input type="text" name="other_name"
                                       value="{{ old('other_name', $student->other_name) }}"
                                       class="form-control">
                            </div>

                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">
                                    Gender <span class="text-danger">*</span>
                                </label>
                                <select name="gender"
                                        class="form-select @error('gender') is-invalid @enderror"
                                        required>
                                    <option value="male"
                                        {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>
                                        Male
                                    </option>
                                    <option value="female"
                                        {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>
                                        Female
                                    </option>
                                </select>
                                @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">Date of Birth</label>
                                <input type="date" name="date_of_birth"
                                       value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
                                       class="form-control">
                            </div>

                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">
                                    Admission Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="admission_number"
                                       value="{{ old('admission_number', $student->admission_number) }}"
                                       class="form-control @error('admission_number') is-invalid @enderror"
                                       required>
                                @error('admission_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium">Address</label>
                                <input type="text" name="address"
                                       value="{{ old('address', $student->address) }}"
                                       class="form-control"
                                       placeholder="Student home address">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Guardian Information --}}
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-semibold mb-0 text-uppercase text-muted"
                            style="font-size:12px;letter-spacing:.05em;">
                            <i class="bi bi-people me-2 text-primary"></i>Guardian Information
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">
                                    Guardian Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="guardian_name"
                                       value="{{ old('guardian_name', $student->guardian_name) }}"
                                       class="form-control @error('guardian_name') is-invalid @enderror"
                                       required>
                                @error('guardian_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">
                                    Guardian Phone <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="guardian_phone"
                                       value="{{ old('guardian_phone', $student->guardian_phone) }}"
                                       class="form-control @error('guardian_phone') is-invalid @enderror"
                                       required>
                                @error('guardian_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium">Guardian Email</label>
                                <input type="email" name="guardian_email"
                                       value="{{ old('guardian_email', $student->guardian_email) }}"
                                       class="form-control"
                                       placeholder="Optional">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── Right Column ── --}}
            <div class="col-12 col-lg-4">

                {{-- Profile Photo --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-semibold mb-0 text-uppercase text-muted"
                            style="font-size:12px;letter-spacing:.05em;">
                            <i class="bi bi-person-badge me-2 text-primary"></i>Profile Photo
                        </h6>
                    </div>
                    <div class="card-body p-4 text-center">

                        {{-- Photo preview --}}
                        <div class="mb-3">
                            @if($student->photo)
                                <img src="{{ asset('storage/' . $student->photo) }}"
                                     id="photoPreview"
                                     class="rounded-circle border shadow-sm"
                                     style="width:110px;height:110px;object-fit:cover;">
                            @else
                                <div id="photoPreview"
                                     class="rounded-circle bg-primary bg-opacity-10 text-primary
                                            fw-bold d-flex align-items-center justify-content-center mx-auto"
                                     style="width:110px;height:110px;font-size:2.2rem;">
                                    {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        {{-- Upload button --}}
                        <label for="photoInput"
                               class="btn btn-outline-primary btn-sm w-100 mb-2"
                               style="cursor:pointer;">
                            <i class="bi bi-upload me-1"></i> Choose Photo
                        </label>
                        <input type="file"
                               id="photoInput"
                               name="photo"
                               accept="image/jpeg,image/png,image/webp"
                               class="d-none @error('photo') is-invalid @enderror">
                        @error('photo')
                        <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                        @enderror

                        {{-- File name display --}}
                        <div id="fileName"
                             class="text-muted mt-1 mb-2 d-none"
                             style="font-size:11px;word-break:break-all;"></div>

                        <small class="text-muted d-block">JPG, PNG or WEBP. Max 2MB.</small>

                        {{-- Remove photo --}}
                        @if($student->photo)
                        <div class="border-top mt-3 pt-3">
                            <div class="form-check text-start">
                                <input class="form-check-input" type="checkbox"
                                       name="remove_photo" id="removePhoto" value="1">
                                <label class="form-check-label text-danger"
                                       for="removePhoto" style="font-size:12px;">
                                    <i class="bi bi-trash me-1"></i>Remove current photo
                                </label>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4 d-grid gap-2">
                        <button type="submit" class="btn btn-primary fw-semibold">
                            <i class="bi bi-floppy me-1"></i> Save Changes
                        </button>
                        <a href="{{ route('admin.students.show', $student) }}"
                           class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('photoInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    // Show file name
    const fileNameEl = document.getElementById('fileName');
    fileNameEl.textContent = file.name;
    fileNameEl.classList.remove('d-none');

    // Live preview
    const reader = new FileReader();
    reader.onload = function (e) {
        const preview = document.getElementById('photoPreview');
        const img     = document.createElement('img');
        img.src             = e.target.result;
        img.id              = 'photoPreview';
        img.className       = 'rounded-circle border shadow-sm';
        img.style.cssText   = 'width:110px;height:110px;object-fit:cover;';
        preview.replaceWith(img);
    };
    reader.readAsDataURL(file);

    // Uncheck remove if they pick a new file
    const removeCheckbox = document.getElementById('removePhoto');
    if (removeCheckbox) removeCheckbox.checked = false;
});

// If remove is checked, reset preview to initials
const removeCheckbox = document.getElementById('removePhoto');
if (removeCheckbox) {
    removeCheckbox.addEventListener('change', function () {
        if (this.checked) {
            const preview  = document.getElementById('photoPreview');
            const initial  = '{{ strtoupper(substr($student->first_name, 0, 1)) }}';
            const div      = document.createElement('div');
            div.id         = 'photoPreview';
            div.className  = 'rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center mx-auto';
            div.style.cssText = 'width:110px;height:110px;font-size:2.2rem;';
            div.textContent = initial;
            preview.replaceWith(div);

            // Clear file input
            document.getElementById('photoInput').value = '';
            document.getElementById('fileName').classList.add('d-none');
        }
    });
}
</script>
@endsection
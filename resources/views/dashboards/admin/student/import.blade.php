@extends('layouts.admin')
@section('title', 'Import Students')
@section('content')
<div class="container-fluid px-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Import Students</h4>
            <small class="text-muted">Upload an Excel or CSV file to bulk add students</small>
        </div>
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Import Errors --}}
    @if(session('import_errors'))
    <div class="card border-warning border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-warning bg-opacity-10 border-bottom border-warning py-3">
            <h6 class="fw-bold mb-0 text-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('import_count') }} imported — {{ count(session('import_errors')) }} rows had errors
            </h6>
        </div>
        <div class="card-body p-0">
            @foreach(session('import_errors') as $error)
            <div class="px-4 py-2 border-bottom" style="font-size:13px;">
                <i class="bi bi-x-circle text-danger me-2"></i>{{ $error }}
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-7">

    {{-- Upload Form --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                <i class="bi bi-file-earmark-arrow-up me-2 text-primary"></i>Upload File
            </h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Class dropdown --}}
                <div class="mb-3">
                    <label class="form-label fw-medium">
                        Assign to Class
                        <span class="text-muted fw-normal" style="font-size:12px;">(optional)</span>
                    </label>
                    <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                        <option value="">-- No class assignment --</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                            @if($class->section) — {{ $class->section->name }} @endif
                        </option>
                        @endforeach
                    </select>
                    @error('class_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">All uploaded students will be assigned to this class in the current term.</div>
                </div>

                {{-- File input --}}
                <div class="mb-4">
                    <label class="form-label fw-medium">Select File <span class="text-danger">*</span></label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv"
                           class="form-control @error('file') is-invalid @enderror" required>
                    @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Accepted formats: .xlsx, .xls, .csv &bull; Max size: 5MB</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-file-earmark-check me-1"></i> Import Students
                    </button>
                    <a href="{{ route('admin.students.download-template') }}"
                       class="btn btn-outline-primary">
                        <i class="bi bi-download me-1"></i> Download Template
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>

        <div class="col-12 col-lg-5">

            {{-- Instructions --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-info-circle me-2 text-primary"></i>File Format Guide
                    </h6>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-3" style="font-size:13px;">
                        Your file must have these column headers in the <strong>first row</strong>:
                    </p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-3" style="font-size:12px;">
                            <thead class="table-light">
                                <tr>
                                    <th>Column</th>
                                    <th>Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td class="font-monospace">first_name</td><td><span class="badge bg-danger">Yes</span></td></tr>
                                <tr><td class="font-monospace">last_name</td><td><span class="badge bg-danger">Yes</span></td></tr>
                                <tr><td class="font-monospace">other_name</td><td><span class="badge bg-secondary">No</span></td></tr>
                                <tr><td class="font-monospace">gender</td><td><span class="badge bg-danger">Yes</span></td></tr>
                                <tr><td class="font-monospace">date_of_birth</td><td><span class="badge bg-secondary">No</span></td></tr>
                                <tr><td class="font-monospace">guardian_name</td><td><span class="badge bg-secondary">No</span></td></tr>
                                <tr><td class="font-monospace">guardian_phone</td><td><span class="badge bg-secondary">No</span></td></tr>
                                <tr><td class="font-monospace">guardian_email</td><td><span class="badge bg-secondary">No</span></td></tr>
                                <tr><td class="font-monospace">address</td><td><span class="badge bg-secondary">No</span></td></tr>
                                <tr><td class="font-monospace">admission_number</td><td><span class="badge bg-secondary">No</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info py-2 px-3 mb-0" style="font-size:12px;">
                        <i class="bi bi-lightbulb me-1"></i>
                        <strong>gender</strong> must be <code>male</code> or <code>female</code>. Leave <strong>admission_number</strong> empty to auto-generate.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
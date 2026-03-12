@extends('layouts.staff')
@section('title', 'Upload Results')
@section('content')
<div class="container-fluid px-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Result Upload</h4>
            <small class="text-muted">
                @if($currentSession && $currentTerm)
                    {{ $currentSession->name }} &mdash; {{ ucfirst($currentTerm->name) }} Term
                @else
                    <span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>No active session/term set</span>
                @endif
            </small>
        </div>
    </div>

    @if(!$currentSession || !$currentTerm)
    <div class="alert alert-warning rounded-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        No active session or term. Please contact your admin to set one.
    </div>
    @endif

    <div class="row g-4 justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-journal-text me-2 text-primary"></i>Select Class & Subject
                    </h6>
                </div>
                <div class="card-body p-4">
                    {{-- GET form submits to uploadForm which then loads the upload page --}}
                    <form action="{{ route('staff.results.upload.form') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select" required>
                                <option value="">-- Select Class --</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-medium">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" class="form-select" required>
                                <option value="">-- Select Subject --</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i> Continue to Upload
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
@extends('layouts.admin')
@section('title', 'Student Profile')
@section('content')
<div class="container-fluid px-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Student Profile</h4>
            <small class="text-muted">{{ $student->admission_number }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">

        {{-- Left --}}
        <div class="col-12 col-lg-4">

            {{-- Profile Card --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4 text-center">
                <div class="card-body p-4">
                    <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center fw-bold mb-3
                        {{ $student->gender === 'male' ? 'bg-primary' : 'bg-danger' }} bg-opacity-10
                        {{ $student->gender === 'male' ? 'text-primary' : 'text-danger' }}"
                        style="width:80px;height:80px;font-size:28px;">
                        {{ strtoupper(substr($student->first_name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $student->full_name }}</h5>
                    <p class="text-muted mb-2" style="font-size:13px;">{{ $student->admission_number }}</p>
                    <span class="badge rounded-pill {{ $student->gender === 'male' ? 'bg-primary' : 'bg-danger' }} bg-opacity-10
                          {{ $student->gender === 'male' ? 'text-primary' : 'text-danger' }} px-3 py-2 me-1">
                        {{ ucfirst($student->gender) }}
                    </span>
                    @if($student->is_active)
                        <span class="badge bg-success rounded-pill px-3 py-2">Active</span>
                    @else
                        <span class="badge bg-secondary rounded-pill px-3 py-2">Inactive</span>
                    @endif
                </div>
            </div>

            {{-- Assign to Class --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-building me-2 text-primary"></i>Assign Class
                    </h6>
                </div>
                <div class="card-body p-4">
                    @if($session && $term)
                    <p style="font-size:12px;" class="text-muted mb-3">
                        <i class="bi bi-broadcast me-1 text-success"></i>
                        {{ $session->name }} — {{ ucfirst($term->name) }} Term
                    </p>
                    <form action="{{ route('admin.students.assign-class', $student) }}" method="POST">
                        @csrf @method('PATCH')
                        <select name="class_id" class="form-select form-select-sm mb-2" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}"
                                {{ $student->currentAssignment?->class_id == $class->id ? 'selected' : '' }}>
                                {{ $class->full_name }}
                            </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-check2 me-1"></i> Assign
                        </button>
                    </form>
                    @else
                    <p class="text-muted mb-0" style="font-size:13px;">
                        <i class="bi bi-exclamation-triangle text-warning me-1"></i>No active session/term set.
                    </p>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3 d-grid gap-2">
                    <form action="{{ route('admin.students.toggle-status', $student) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="btn w-100 btn-sm {{ $student->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                onclick="return confirm('{{ $student->is_active ? 'Deactivate' : 'Activate' }} this student?')">
                            <i class="bi {{ $student->is_active ? 'bi-person-dash' : 'bi-person-check' }} me-1"></i>
                            {{ $student->is_active ? 'Deactivate Student' : 'Activate Student' }}
                        </button>
                    </form>
                    <hr class="my-1">
                    <form action="{{ route('admin.students.destroy', $student) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm w-100"
                                onclick="return confirm('Permanently delete {{ $student->short_name }}?')">
                            <i class="bi bi-trash me-1"></i> Delete Student
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- Right --}}
        <div class="col-12 col-lg-8">

            {{-- Personal Details --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-person me-2 text-primary"></i>Personal Details
                    </h6>
                </div>
                <div class="card-body p-0">
                    <dl class="mb-0">
                        <div class="row g-0 px-4 py-3 border-bottom">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Full Name</dt>
                            <dd class="col-7 col-sm-8 mb-0 fw-semibold" style="font-size:14px;">{{ $student->full_name }}</dd>
                        </div>
                        <div class="row g-0 px-4 py-3 border-bottom">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Gender</dt>
                            <dd class="col-7 col-sm-8 mb-0" style="font-size:14px;">{{ ucfirst($student->gender) }}</dd>
                        </div>
                        <div class="row g-0 px-4 py-3 border-bottom">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Date of Birth</dt>
                            <dd class="col-7 col-sm-8 mb-0" style="font-size:14px;">
                                {{ $student->date_of_birth?->format('d M Y') ?? '—' }}
                            </dd>
                        </div>
                        <div class="row g-0 px-4 py-3 border-bottom">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Address</dt>
                            <dd class="col-7 col-sm-8 mb-0" style="font-size:14px;">{{ $student->address ?? '—' }}</dd>
                        </div>
                        <div class="row g-0 px-4 py-3 border-bottom">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Guardian</dt>
                            <dd class="col-7 col-sm-8 mb-0" style="font-size:14px;">
                                {{ $student->guardian_name }}<br>
                                <small class="text-muted">{{ $student->guardian_phone }}</small>
                                @if($student->guardian_email)
                                <br><small class="text-muted">{{ $student->guardian_email }}</small>
                                @endif
                            </dd>
                        </div>
                        <div class="row g-0 px-4 py-3">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Enrolled</dt>
                            <dd class="col-7 col-sm-8 mb-0 text-muted" style="font-size:14px;">
                                {{ $student->created_at->format('d M Y') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Class History --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-clock-history me-2 text-primary"></i>Class History
                    </h6>
                </div>
                <div class="card-body p-0">
                    @forelse($student->classAssignments->sortByDesc('created_at') as $assignment)
                    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:36px;height:36px;font-size:13px;">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="font-size:14px;">
                                {{ $assignment->schoolClass?->full_name ?? '—' }}
                            </div>
                            <small class="text-muted">
                                {{ $assignment->session?->name }} &bull; {{ ucfirst($assignment->term?->name) }} Term
                            </small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4" style="font-size:13px;">
                        <i class="bi bi-building d-block fs-3 opacity-25 mb-2"></i>
                        No class assignments yet.
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
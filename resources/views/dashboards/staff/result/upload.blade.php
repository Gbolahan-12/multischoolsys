@extends('layouts.staff')
@section('title', 'Upload Results')
@section('content')
<div class="container-fluid px-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Upload Results</h4>
            <small class="text-muted">
                <strong>{{ $subject->name }}</strong> &mdash; {{ $class->full_name }}
                @if($currentTerm) &mdash; {{ ucfirst($currentTerm->name) }} Term @endif
                @if($currentSession) &mdash; {{ $currentSession->name }} @endif
            </small>
        </div>
        <a href="{{ route('staff.results.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('import_errors'))
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-warning bg-opacity-10 border-bottom py-3">
            <h6 class="fw-bold mb-0 text-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ count(session('import_errors')) }} rows had errors
            </h6>
        </div>
        <div class="card-body p-0">
            @foreach(session('import_errors') as $err)
            <div class="px-4 py-2 border-bottom" style="font-size:13px;">
                <i class="bi bi-x-circle text-danger me-2"></i>{{ $err }}
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="row g-4">

        {{-- LEFT: Upload --}}
        <div class="col-12 col-lg-5">

            {{-- Score breakdown --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-bar-chart me-2 text-primary"></i>Score Breakdown
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-2 text-center mb-3">
                        <div class="col-3">
                            <div class="rounded-3 bg-primary bg-opacity-10 p-2">
                                <div class="fw-bold text-white fs-5">20</div>
                                <small class="text-muted" style="font-size:11px;">CA1</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="rounded-3 bg-info bg-opacity-10 p-2">
                                <div class="fw-bold text-white fs-5">20</div>
                                <small class="text-muted" style="font-size:11px;">CA2</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="rounded-3 bg-success bg-opacity-10 p-2">
                                <div class="fw-bold text-white fs-5">60</div>
                                <small class="text-muted" style="font-size:11px;">Exam</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="rounded-3 bg-warning bg-opacity-10 p-2">
                                <div class="fw-bold text-white fs-5">100</div>
                                <small class="text-muted" style="font-size:11px;">Total</small>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info py-2 px-3 mb-0" style="font-size:12px;">
                        <i class="bi bi-lightbulb me-1"></i>
                        You can upload each assessment separately. CA1 today, CA2 next week — existing scores are preserved.
                    </div>
                </div>
            </div>

            {{-- Upload form --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-cloud-upload me-2 text-primary"></i>Upload CSV
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('staff.results.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="class_id"   value="{{ $class->id }}">
                        <input type="hidden" name="subject_id" value="{{ $subject->id }}">

                        {{-- Component selector --}}
                        <div class="mb-3">
                            <label class="form-label fw-medium">Which Assessment? <span class="text-danger">*</span></label>
                            <select name="component" id="componentSelect"
                                    class="form-select @error('component') is-invalid @enderror" required>
                                <option value="">-- Select --</option>
                                <option value="ca1"  {{ old('component') === 'ca1'  ? 'selected' : '' }}>CA1 (max 20)</option>
                                <option value="ca2"  {{ old('component') === 'ca2'  ? 'selected' : '' }}>CA2 (max 20)</option>
                                <option value="exam" {{ old('component') === 'exam' ? 'selected' : '' }}>Exam (max 60)</option>
                            </select>
                            @error('component')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">CSV File <span class="text-danger">*</span></label>
                            <input type="file" name="file" accept=".csv"
                                   class="form-control @error('file') is-invalid @enderror" required>
                            @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">CSV only &bull; Max 5MB</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-cloud-upload me-1"></i> Upload
                            </button>
                        </div>
                    </form>

                    {{-- Download template --}}
                    <hr class="my-3">
                    <p class="fw-semibold text-muted mb-2" style="font-size:12px;">DOWNLOAD TEMPLATE:</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('staff.results.download-template', ['class_id' => $class->id, 'subject_id' => $subject->id, 'component' => 'ca1']) }}"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-download me-1"></i> CA1 Template
                        </a>
                        <a href="{{ route('staff.results.download-template', ['class_id' => $class->id, 'subject_id' => $subject->id, 'component' => 'ca2']) }}"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-download me-1"></i> CA2 Template
                        </a>
                        <a href="{{ route('staff.results.download-template', ['class_id' => $class->id, 'subject_id' => $subject->id, 'component' => 'exam']) }}"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-download me-1"></i> Exam Template
                        </a>
                    </div>

                    <div class="mt-3">
                        <p class="fw-semibold text-muted mb-2" style="font-size:12px;">CSV COLUMNS (per template):</p>
                        <table class="table table-sm table-bordered mb-0" style="font-size:11px;">
                            <tbody>
                                <tr><td class="font-monospace">admission_number</td><td>Required</td></tr>
                                <tr><td class="font-monospace">student_name</td><td>Reference only</td></tr>
                                <tr><td class="font-monospace">ca1_score / ca2_score / exam_score</td><td>The chosen component</td></tr>
                                <tr><td class="font-monospace">bonus_mark</td><td>Optional</td></tr>
                                <tr><td class="font-monospace">bonus_component</td><td>Same as upload component</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Students & current scores --}}
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-people me-2 text-primary"></i>
                        {{ $class->full_name }} — {{ $students->count() }} Students
                    </h6>
                    @if($existingResults->isNotEmpty())
                    <a href="{{ route('staff.results.view', ['class_id' => $class->id, 'subject_id' => $subject->id]) }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye me-1"></i> Full View
                    </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:12px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-2">Student</th>
                                    <th class="py-2 text-center">CA1<br><small class="fw-normal text-muted">/20</small></th>
                                    <th class="py-2 text-center">CA2<br><small class="fw-normal text-muted">/20</small></th>
                                    <th class="py-2 text-center">Exam<br><small class="fw-normal text-muted">/60</small></th>
                                    <th class="py-2 text-center">Total</th>
                                    <th class="py-2 text-center pe-4">Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                @php $result = $existingResults->get($student->id); @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold">{{ $student->short_name }}</div>
                                        <small class="text-muted">{{ $student->admission_number }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if($result && $result->ca1_score !== null)
                                            <span class="text-success fw-semibold">{{ $result->ca1_score }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($result && $result->ca2_score !== null)
                                            <span class="text-success fw-semibold">{{ $result->ca2_score }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($result && $result->exam_score !== null)
                                            <span class="text-success fw-semibold">{{ $result->exam_score }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold">{{ $result ? $result->total_score : '—' }}</td>
                                    <td class="text-center pe-4">
                                        @if($result && $result->grade)
                                        <span class="badge rounded-pill
                                            {{ match($result->grade) {
                                                'A' => 'bg-success',
                                                'B' => 'bg-primary',
                                                'C' => 'bg-info text-dark',
                                                'D' => 'bg-warning text-dark',
                                                'E' => 'bg-secondary',
                                                default => 'bg-danger'
                                            } }}">
                                            {{ $result->grade }}
                                        </span>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-people fs-2 d-block opacity-25 mb-2"></i>
                                        No students found in this class for the current term.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
@extends('layouts.staff')
@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-4 py-2">

    {{-- ── Term Banner ────────────────────────────────────────── --}}
    <div class="term-banner rounded-3 mb-4 p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <div class="term-icon">
                <i class="bi bi-calendar-event"></i>
            </div>
            <div>
                @if($currentSession && $currentTerm)
                    <div class="fw-bold text-white" style="font-size:15px;">
                        {{ $currentSession->name }} &mdash; {{ ucfirst($currentTerm->name) }} Term
                    </div>
                    <small class="text-white-50">
                        {{ \Carbon\Carbon::parse($currentTerm->start_date)->format('d M') }}
                        &ndash;
                        {{ \Carbon\Carbon::parse($currentTerm->end_date)->format('d M Y') }}
                    </small>
                @else
                    <div class="fw-bold text-white">No active term set</div>
                    <small class="text-white-50">Contact your administrator</small>
                @endif
            </div>
        </div>
        <a href="{{ route('staff.results.index') }}"
           class="btn btn-light btn-sm fw-semibold px-3">
            <i class="feather-upload-cloud me-1"></i> Upload Results
        </a>
    </div>

    {{-- ── Stat Cards ──────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">

        {{-- Subjects assigned --}}
        <div class="col-6 col-lg-4">
            <div class="stat-card card border-0 rounded-3 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="stat-icon bg-primary-soft text-primary">
                            <i class="bi bi-book"></i>
                        </div>
                        <span class="badge bg-primary-soft text-primary rounded-pill" style="font-size:10px;">This Term</span>
                    </div>
                    <div class="stat-number">{{ $mySubjects->count() }}</div>
                    <div class="stat-label">Subjects / Classes</div>
                </div>
            </div>
        </div>

        {{-- Results uploaded --}}
        <div class="col-6 col-lg-4">
            <div class="stat-card card border-0 rounded-3 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="stat-icon bg-success-soft text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <span class="badge bg-success-soft text-success rounded-pill" style="font-size:10px;">Uploaded</span>
                    </div>
                    <div class="stat-number">{{ $totalResultsUploaded }}</div>
                    <div class="stat-label">Results This Term</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="stat-card card border-0 rounded-3 h-100 {{ $pendingUploads > 0 ? 'border-warning-subtle' : '' }}">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="stat-icon {{ $pendingUploads > 0 ? 'bg-warning-soft text-warning' : 'bg-secondary-soft text-secondary' }}">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        @if($pendingUploads > 0)
                        <span class="badge bg-warning text-dark rounded-pill" style="font-size:10px;">Action needed</span>
                        @endif
                    </div>
                    <div class="stat-number {{ $pendingUploads > 0 ? 'text-warning' : '' }}">{{ $pendingUploads }}</div>
                    <div class="stat-label">Pending Uploads</div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Two Column: Subjects Table + Recent Results ─────────── --}}
    <div class="row g-4">

        {{-- My Subjects / Classes --}}
        <div class="col-12 col-xl-5">
            <div class="card border-0 rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="section-title mb-0">
                        <i class="feather-layers me-2 text-primary"></i>My Subjects
                    </h6>
                    <a href="{{ route('staff.results.index') }}" class="btn btn-primary btn-sm px-3">
                        <i class="feather-plus me-1"></i> Upload
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($mySubjects as $item)
                    <div class="subject-row d-flex align-items-center justify-content-between px-4 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="subject-dot" style="background:{{ $item['color'] }}"></div>
                            <div>
                                <div class="fw-semibold" style="font-size:14px;">{{ $item['subject'] }}</div>
                                <small class="text-muted">{{ $item['class'] }}</small>
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            @foreach(['ca1','ca2','exam'] as $comp)
                                <span class="component-pill {{ $item['uploaded'][$comp] ? 'done' : 'missing' }}"
                                      title="{{ strtoupper($comp) }}: {{ $item['uploaded'][$comp] ? 'Uploaded' : 'Missing' }}">
                                    {{ strtoupper($comp) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="feather-inbox d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
                        <small>No subjects assigned yet</small>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Results --}}
        <div class="col-12 col-xl-7">
            <div class="card border-0 rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="section-title mb-0">
                        <i class="feather-clock me-2 text-primary"></i>Recently Uploaded
                    </h6>
                    <a href="{{ route('staff.results.view') }}" class="btn btn-outline-secondary btn-sm px-3">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-2">Student</th>
                                    <th class="py-2">Subject</th>
                                    <th class="py-2 text-center">CA1</th>
                                    <th class="py-2 text-center">CA2</th>
                                    <th class="py-2 text-center">Exam</th>
                                    <th class="py-2 text-center">Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentResults as $result)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold">{{ $result->student->short_name ?? $result->student->first_name }}</div>
                                        <small class="text-muted">{{ $result->student->admission_number }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $result->subject->name }}</div>
                                        <small class="text-muted">{{ $result->schoolClass->full_name ?? $result->schoolClass->name }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if($result->ca1_score !== null)
                                            <span class="text-success fw-semibold">{{ $result->ca1_score }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($result->ca2_score !== null)
                                            <span class="text-success fw-semibold">{{ $result->ca2_score }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($result->exam_score !== null)
                                            <span class="text-success fw-semibold">{{ $result->exam_score }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $grade = $result->grade;
                                            $cls = match($grade) {
                                                'A' => 'bg-success',
                                                'B' => 'bg-primary',
                                                'C' => 'bg-info',
                                                'D' => 'bg-warning text-dark',
                                                'E' => 'bg-secondary',
                                                default => 'bg-danger',
                                            };
                                        @endphp
                                        @if($grade)
                                            <span class="badge {{ $cls }} rounded-pill">{{ $grade }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="feather-inbox d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
                                        No results uploaded yet this term.
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

<style>
/* ── Term Banner ── */
.term-banner {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    box-shadow: 0 4px 15px rgba(13,110,253,.25);
}
.term-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: rgba(255,255,255,.15);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 18px;
}

/* ── Stat Cards ── */
.stat-card {
    background: #fff;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    transition: transform .15s, box-shadow .15s;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,.1);
}
.stat-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
}
.stat-number {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 4px;
}
.stat-label {
    font-size: 12px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: .04em;
}

/* Soft colour helpers */
.bg-primary-soft  { background: rgba(13,110,253,.1); }
.bg-success-soft  { background: rgba(25,135,84,.1); }
.bg-warning-soft  { background: rgba(255,193,7,.15); }
.bg-secondary-soft{ background: rgba(108,117,125,.1); }
.text-primary  { color: #0d6efd !important; }
.text-success  { color: #198754 !important; }
.text-warning  { color: #ffc107 !important; }

/* ── Subject Rows ── */
.subject-row {
    border-bottom: 1px solid #f0f0f0;
    transition: background .12s;
}
.subject-row:last-child { border-bottom: none; }
.subject-row:hover { background: #f8f9ff; }
.subject-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

/* ── Component Pills ── */
.component-pill {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: .03em;
}
.component-pill.done    { background: #d1fae5; color: #065f46; }
.component-pill.missing { background: #f1f3f5; color: #adb5bd; }

/* ── Section title ── */
.section-title {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #495057;
}
</style>
@endsection
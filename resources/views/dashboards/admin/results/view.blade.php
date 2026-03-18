@extends('layouts.admin')
@section('title', 'View Results')
@section('content')
<div class="container-fluid px-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">{{ $subject->name }} — Results</h4>
            <small class="text-muted">
                {{ $class->full_name }}
                @if($currentTerm) &mdash; {{ ucfirst($currentTerm->name) }} Term @endif
                @if($currentSession) &mdash; {{ $currentSession->name }} @endif
            </small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.results.upload.form', ['class_id' => $class->id, 'subject_id' => $subject->id]) }}"
               class="btn btn-primary btn-sm">
                <i class="bi bi-cloud-upload me-1"></i> Upload More
            </a>
            <a href="{{ route('admin.results.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 text-center">
                <div class="card-body p-3">
                    <div class="fw-bold fs-4 text-primary">{{ $results->count() }}</div>
                    <small class="text-muted">Students</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 text-center">
                <div class="card-body p-3">
                    <div class="fw-bold fs-4 text-success">{{ $stats['highest'] }}</div>
                    <small class="text-muted">Highest</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 text-center">
                <div class="card-body p-3">
                    <div class="fw-bold fs-4 text-warning">{{ $stats['average'] }}</div>
                    <small class="text-muted">Class Average</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 text-center">
                <div class="card-body p-3">
                    <div class="fw-bold fs-4 text-danger">{{ $stats['failed'] }}</div>
                    <small class="text-muted">Failed</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3" style="font-size:12px;">#</th>
                            <th class="py-3" style="font-size:12px;">Student</th>
                            <th class="py-3 text-center" style="font-size:12px;">CA1<br><small class="fw-normal text-muted">/20</small></th>
                            <th class="py-3 text-center" style="font-size:12px;">CA2<br><small class="fw-normal text-muted">/20</small></th>
                            <th class="py-3 text-center" style="font-size:12px;">Exam<br><small class="fw-normal text-muted">/60</small></th>
                            {{-- <th class="py-3 text-center" style="font-size:12px;">Bonus</th> --}}
                            <th class="py-3 text-center" style="font-size:12px;">Total<br><small class="fw-normal text-muted">/100</small></th>
                            <th class="py-3 text-center" style="font-size:12px;">Grade</th>
                            <th class="py-3 text-center d-none d-md-table-cell" style="font-size:12px;">Remark</th>
                            <th class="py-3 pe-4 text-end" style="font-size:12px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $i => $result)
                        <tr>
                            <td class="ps-4 text-muted" style="font-size:13px;">{{ $i + 1 }}</td>
                            <td>
                                <div class="fw-semibold" style="font-size:14px;">{{ $result->student->short_name }}</div>
                                <small class="text-muted">{{ $result->student->admission_number }}</small>
                            </td>
                            <td class="text-center" style="font-size:14px;">
                                {{ $result->ca1_score !== null ? $result->ca1_score : '—' }}
                            </td>
                            <td class="text-center" style="font-size:14px;">
                                {{ $result->ca2_score !== null ? $result->ca2_score : '—' }}
                            </td>
                            <td class="text-center" style="font-size:14px;">
                                {{ $result->exam_score !== null ? $result->exam_score : '—' }}
                            </td>
                            {{-- <td class="text-center" style="font-size:13px;">
                                @if($result->bonus_mark > 0)
                                    <span class="text-warning fw-semibold">+{{ $result->bonus_mark }}</span>
                                    <small class="text-muted d-block">on {{ strtoupper($result->bonus_component) }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td> --}}
                            <td class="text-center fw-bold fs-6">{{ $result->total_score }}</td>
                            <td class="text-center">
                                @if($result->grade)
                                <span class="badge rounded-pill px-3
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
                            <td class="text-center d-none d-md-table-cell text-muted" style="font-size:13px;">
                                {{ $result->remark ?? '—' }}
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('admin.results.edit', $result) }}"
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.results.destroy', $result) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Delete result for {{ addslashes($result->student->short_name) }}?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block opacity-25 mb-3"></i>
                                No results uploaded yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
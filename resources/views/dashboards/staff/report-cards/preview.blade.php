@extends('layouts.staff')
@section('title', 'Preview Report Cards')

@section('content')
    <div class="container-fluid px-4 py-3">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('staff.report-cards.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="fw-bold mb-0">Preview: {{ $class->name }} — {{ ucfirst($term->name) }} Term</h4>
                    <small class="text-muted">{{ $students->count() }} student(s) &bull;
                        {{ $term->session->name ?? '' }}</small>
                </div>
            </div>
            <form action="{{ route('staff.report-cards.download') }}" method="GET">
                <input type="hidden" name="class_id" value="{{ $class->id }}">
                <input type="hidden" name="term_id" value="{{ $term->id }}">
                <button class="btn btn-success fw-semibold px-4">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Download PDF
                </button>
            </form>
        </div>

        @foreach($students as $student)
            @php
                $aggregate = 0;
                $subCount = 0;
                foreach ($subjects as $subject) {
                    $r = $resultMap[$student->id][$subject->id] ?? null;
                    if ($r) {
                        $aggregate += $r->total_score ?? 0;
                        $subCount++;
                    }
                }
                $average = $subCount > 0 ? round($aggregate / $subCount, 1) : 0;
                $position = $positions[$student->id] ?? '—';
                $total = count($students);
                $suffix = match (true) {
                    $position % 100 >= 11 && $position % 100 <= 13 => 'th',
                    $position % 10 === 1 => 'st',
                    $position % 10 === 2 => 'nd',
                    $position % 10 === 3 => 'rd',
                    default => 'th',
                };
                $overallGrade = 'F';
                $overallRemark = 'Fail';
                foreach ($gradeSystem as $g) {
                    if ($average >= $g['min'] && $average <= $g['max']) {
                        $overallGrade = $g['grade'];
                        $overallRemark = $g['remark'];
                        break;
                    }
                }
            @endphp

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center"
                            style="width:36px;height:36px;font-size:14px;flex-shrink:0;">
                            {{ strtoupper(substr($student->first_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold">{{ $student->last_name }}, {{ $student->first_name }}</div>
                            <small class="text-muted">{{ $student->admission_number }}</small>
                        </div>
                    </div>
                    <div class="d-flex gap-3 text-center" style="font-size:12px;">
                        <div>
                            <div class="fw-bold text-primary" style="font-size:16px;">{{ $average }}%</div>
                            <div class="text-muted">Average</div>
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:16px;">
                                {{ $position }}<sup style="font-size:10px;">{{ $suffix }}</sup>/{{ $total }}
                            </div>
                            <div class="text-muted">Position</div>
                        </div>
                        <div>
                            @php
                                $badgeColor = match ($overallGrade) {
                                    'A' => 'success', 'B' => 'primary', 'C' => 'info',
                                    'D' => 'warning', 'E' => 'secondary', default => 'danger'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeColor }} rounded-pill" style="font-size:14px;padding:6px 12px;">
                                {{ $overallGrade }}
                            </span>
                            <div class="text-muted mt-1">Grade</div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-2">Subject</th>
                                    <th class="py-2 text-center">CA 1</th>
                                    <th class="py-2 text-center">CA 2</th>
                                    <th class="py-2 text-center">Exam</th>
                                    <th class="py-2 text-center">Bonus</th>
                                    <th class="py-2 text-center">Total</th>
                                    <th class="py-2 text-center">Class Avg</th>
                                    <th class="py-2 text-center">Grade</th>
                                    <th class="py-2 text-center">Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subjects as $subject)
                                    @php
                                        $r = $resultMap[$student->id][$subject->id] ?? null;
                                        $subRemark = '—';
                                        if ($r && $r->grade) {
                                            foreach ($gradeSystem as $g) {
                                                if ($g['grade'] === $r->grade) {
                                                    $subRemark = $g['remark'];
                                                    break;
                                                }
                                            }
                                        }
                                        $badgeColors = ['A' => 'success', 'B' => 'primary', 'C' => 'info', 'D' => 'warning', 'E' => 'secondary', 'F' => 'danger'];
                                        $bc = $badgeColors[$r->grade ?? 'F'] ?? 'secondary';
                                    @endphp
                                    <tr>
                                        <td class="ps-4 fw-semibold">{{ $subject->name }}</td>
                                        <td class="text-center">{{ $r ? ($r->ca1_score ?? '—') : '—' }}</td>
                                        <td class="text-center">{{ $r ? ($r->ca2_score ?? '—') : '—' }}</td>
                                        <td class="text-center">{{ $r ? ($r->exam_score ?? '—') : '—' }}</td>
                                        <td class="text-center text-muted">{{ $r && $r->bonus_mark > 0 ? $r->bonus_mark : '—' }}
                                        </td>
                                        <td class="text-center fw-bold">{{ $r ? ($r->total_score ?? '—') : '—' }}</td>
                                        @php
                                            $classAvg = $classAverages[$subject->id] ?? null;
                                            $avgBadge = '';
                                            if ($classAvg && $r && $r->total_score !== null) {
                                                $avgBadge = (float) $r->total_score >= $classAvg ? 'text-success' : 'text-danger';
                                            }
                                        @endphp
                                        <td class="text-center fw-semibold {{ $avgBadge }}">
                                            {{ $classAvg ?? '—' }}
                                        </td>
                                        <td class="text-center">
                                            @if($r && $r->grade)
                                                <span class="badge bg-{{ $bc }} rounded-pill">{{ $r->grade }}</span>
                                            @else —
                                            @endif
                                        </td>
                                        <td class="text-center text-muted">{{ $subRemark }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light fw-bold">
                                    <td class="ps-4">AGGREGATE</td>
                                    <td colspan="4" class="text-end text-muted" style="font-size:11px;">Overall Average:</td>
                                    <td class="text-center">{{ $average }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $badgeColor }} rounded-pill">{{ $overallGrade }}</span>
                                    </td>
                                    <td class="text-center">{{ $overallRemark }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
@extends(auth()->user()->role === 'proprietor'
    ? 'layouts.proprietor'
    : 'layouts.admin')
@section('title', 'Dashboard')
@section('content')
<div class="container-fluid px-4">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Admin Dashboard</h4>
            <small class="text-muted">
                @if($currentSession && $currentTerm)
                    {{ $currentSession->name }} &mdash; {{ ucfirst($currentTerm->name) }} Term
                @else
                    <span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>No active session/term</span>
                @endif
            </small>
        </div>
        <div class="text-muted" style="font-size:13px;">
            <i class="bi bi-calendar3 me-1"></i>{{ now()->format('l, d M Y') }}
        </div>
    </div>

{{-- ── Stat Cards Row 1 — Core Stats ── --}}
<div class="row g-3 mb-3">

    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 bg-primary bg-opacity-10 p-2">
                        <i class="bi bi-people-fill fs-5 text-white"></i>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-white rounded-pill" style="font-size:11px;">Active</span>
                </div>
                <h3 class="fw-bold mb-0">{{ number_format($totalStudents) }}</h3>
                <small class="text-muted">Total Students</small>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 bg-success bg-opacity-10 p-2">
                        <i class="bi bi-person-hearts fs-5 text-white"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-white rounded-pill" style="font-size:11px;">Active</span>
                </div>
                <h3 class="fw-bold mb-0">{{ number_format($totalStaff, 0) }}</h3>
                <small class="text-muted">Total Staff</small>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 bg-danger bg-opacity-10 p-2">
                        <i class="bi bi-exclamation-circle-fill fs-5 text-white"></i>
                    </div>
                    <span class="badge bg-danger bg-opacity-10 text-white rounded-pill" style="font-size:11px;">Owing</span>
                </div>
                <h3 class="fw-bold mb-0">{{ number_format($studentsOwing) }}</h3>
                <small class="text-muted">Defaulters</small>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 bg-info bg-opacity-10 p-2">
                        <i class="bi bi-file-earmark-bar-graph fs-5 text-white"></i>
                    </div>
                    <span class="badge bg-info bg-opacity-10 text-white rounded-pill" style="font-size:11px;">This Term</span>
                </div>
                <h3 class="fw-bold mb-0">{{ number_format($resultsUploaded) }}</h3>
                <small class="text-muted">Results Uploaded</small>
            </div>
        </div>
    </div>

</div>

{{-- ── Stat Cards Row 2 — Financial Summary (Compulsory Fees) ── --}}
<div class="row g-3 mb-4">

    {{-- Section label --}}
    <div class="col-12">
        <small class="text-muted fw-semibold text-uppercase" style="font-size:11px;letter-spacing:.08em;">
            <i class="bi bi-shield-check me-1 text-primary"></i>
            Compulsory Fee Summary —
            {{ $currentTerm ? ucfirst($currentTerm->name) . ' Term' : 'Current Term' }}
            @if($currentSession) &bull; {{ $currentSession->name }} @endif
        </small>
    </div>

    {{-- Defaulters --}}
    {{-- <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100"
             style="border-left:3px solid #dc3545 !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 bg-danger bg-opacity-10 p-2">
                        <i class="bi bi-person-x-fill fs-5 text-danger"></i>
                    </div>
                    <a href="{{ route('admin.payments.defaulters') }}"
                       class="badge bg-danger bg-opacity-10 text-danger rounded-pill text-decoration-none"
                       style="font-size:11px;">
                        View <i class="bi bi-arrow-right ms-1" style="font-size:9px;"></i>
                    </a>
                </div>
                <h3 class="fw-bold mb-0 text-danger">{{ number_format($defaultersCount) }}</h3>
                <small class="text-muted">Defaulters</small>
                <div class="mt-2" style="font-size:11px;color:#9ca3af;">
                    Students with unpaid compulsory fees
                </div>
            </div>
        </div>
    </div> --}}

    {{-- Amount Expected --}}
    <div class="col-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100"
             style="border-left:3px solid #1A69AE !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 bg-primary bg-opacity-10 p-2">
                        <i class="bi bi-calculator fs-5 text-primary"></i>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill" style="font-size:11px;">
                        Expected
                    </span>
                </div>
                <h3 class="fw-bold mb-0">₦{{ number_format($amountExpected, 0) }}</h3>
                <small class="text-muted">Amount Expected</small>
                <div class="mt-2" style="font-size:11px;color:#9ca3af;">
                    Total compulsory fees this term
                </div>
            </div>
        </div>
    </div>

    {{-- Amount Paid --}}
    <div class="col-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100"
             style="border-left:3px solid #198754 !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 bg-success bg-opacity-10 p-2">
                        <i class="bi bi-check-circle-fill fs-5 text-white"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-white rounded-pill" style="font-size:11px;">
                        Collected
                    </span>
                </div>
                <h3 class="fw-bold mb-0 text-success">₦{{ number_format($amountPaid, 0) }}</h3>
                <small class="text-muted">Amount Paid</small>
                @if($amountExpected > 0)
                <div class="mt-2">
                    @php $paidPct = min(100, round(($amountPaid / $amountExpected) * 100)); @endphp
                    <div class="progress rounded-pill" style="height:4px;">
                        <div class="progress-bar bg-success" style="width:{{ $paidPct }}%"></div>
                    </div>
                    <div style="font-size:11px;color:#9ca3af;margin-top:3px;">
                        {{ $paidPct }}% collected
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Amount Remaining --}}
    <div class="col-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100"
             style="border-left:3px solid #f59f00 !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 bg-warning bg-opacity-10 p-2">
                        <i class="bi bi-hourglass-split fs-5 text-white"></i>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-white rounded-pill" style="font-size:11px;">
                        Pending
                    </span>
                </div>
                <h3 class="fw-bold mb-0 text-warning">₦{{ number_format($amountRemaining, 0) }}</h3>
                <small class="text-muted">Amount Remaining</small>
                @if($amountExpected > 0)
                <div class="mt-2">
                    @php $remainPct = min(100, round(($amountRemaining / $amountExpected) * 100)); @endphp
                    <div class="progress rounded-pill" style="height:4px;">
                        <div class="progress-bar bg-warning" style="width:{{ $remainPct }}%"></div>
                    </div>
                    <div style="font-size:11px;color:#9ca3af;margin-top:3px;">
                        {{ $remainPct }}% still outstanding
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
    {{-- Charts Row 1 --}}
    <div class="row g-4 mb-4">

        {{-- Fee Collection Bar Chart --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-bar-chart me-2 text-primary"></i>Fee Collection — Last 6 Months
                    </h6>
                </div>
                <div class="card-body p-4">
                    <canvas id="feeChart" height="100"></canvas>
                </div>
            </div>
        </div>

        {{-- Payment Status Donut --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-pie-chart me-2 text-primary"></i>Payment Status
                        @if($currentTerm) <span class="fw-normal text-muted">({{ ucfirst($currentTerm->name) }} Term)</span> @endif
                    </h6>
                </div>
                <div class="card-body p-4 d-flex flex-column align-items-center">
                    <canvas id="statusChart" height="180" style="max-width:220px;"></canvas>
                    <div class="mt-3 d-flex gap-3 flex-wrap justify-content-center" style="font-size:13px;">
                        <span><span class="badge bg-success me-1">&nbsp;</span>Paid: {{ $statusPaid }}</span>
                        <span><span class="badge bg-warning text-dark me-1">&nbsp;</span>Partial: {{ $statusPartial }}</span>
                        <span><span class="badge bg-danger me-1">&nbsp;</span>Owing: {{ $statusOwing }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Charts Row 2 --}}
    <div class="row g-4 mb-4">

        {{-- Enrollment Growth --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-graph-up me-2 text-primary"></i>Student Enrollment — Last 6 Months
                    </h6>
                </div>
                <div class="card-body p-4">
                    <canvas id="enrollmentChart" height="120"></canvas>
                </div>
            </div>
        </div>

        {{-- Grade Distribution --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-award me-2 text-primary"></i>Grade Distribution
                        @if($currentTerm) <span class="fw-normal text-muted">({{ ucfirst($currentTerm->name) }} Term)</span> @endif
                    </h6>
                </div>
                <div class="card-body p-4">
                    <canvas id="gradeChart" height="120"></canvas>
                </div>
            </div>
        </div>

    </div>

    {{-- Recent Payments --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                <i class="bi bi-clock-history me-2 text-primary"></i>Recent Payments
            </h6>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-primary btn-sm">
                View All
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-2">Student</th>
                            <th class="py-2">Fee</th>
                            <th class="py-2 text-end">Amount</th>
                            <th class="py-2 text-center">Method</th>
                            <th class="py-2 text-center">Status</th>
                            <th class="py-2 pe-4 text-end">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments as $payment)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $payment->student->short_name }}</div>
                                <small class="text-muted">{{ $payment->student->admission_number }}</small>
                            </td>
                            <td class="text-muted">{{ $payment->fee->feeType->name }}</td>
                            <td class="text-end fw-semibold text-success">₦{{ number_format($payment->amount_paid, 0) }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary bg-opacity-75 text-capitalize">
                                    {{ str_replace('_', ' ', $payment->payment_method) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill
                                    {{ match($payment->status) {
                                        'paid'    => 'bg-success',
                                        'partial' => 'bg-warning text-dark',
                                        default   => 'bg-danger'
                                    } }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4 text-muted">
                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox d-block fs-3 opacity-25 mb-2"></i>
                                No payments recorded this term.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const gridColor  = 'rgba(0,0,0,0.05)';
const fontFamily = "'Inter', 'Segoe UI', sans-serif";

// ── 1. Fee Collection Bar Chart ──────────────────────────────
new Chart(document.getElementById('feeChart'), {
    type: 'bar',
    data: {
        labels: @json($feeChartLabels),
        datasets: [{
            label: 'Fees Collected (₦)',
            data:  @json($feeChartData),
            backgroundColor: 'rgba(13,110,253,0.15)',
            borderColor:     'rgba(13,110,253,0.8)',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => '₦' + Number(ctx.raw).toLocaleString()
                }
            }
        },
        scales: {
            y: {
                grid: { color: gridColor },
                ticks: {
                    font: { family: fontFamily, size: 11 },
                    callback: v => '₦' + Number(v).toLocaleString()
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { family: fontFamily, size: 11 } }
            }
        }
    }
});

// ── 2. Payment Status Donut ───────────────────────────────────
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Paid', 'Partial', 'Owing'],
        datasets: [{
            data: [{{ $statusPaid }}, {{ $statusPartial }}, {{ $statusOwing }}],
            backgroundColor: ['#198754','#ffc107','#dc3545'],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true,
        cutout: '70%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.label + ': ' + ctx.raw + ' students'
                }
            }
        }
    }
});

// ── 3. Enrollment Growth Line Chart ──────────────────────────
new Chart(document.getElementById('enrollmentChart'), {
    type: 'line',
    data: {
        labels: @json($enrollmentLabels),
        datasets: [{
            label: 'New Students',
            data:  @json($enrollmentData),
            borderColor: '#0dcaf0',
            backgroundColor: 'rgba(13,202,240,0.1)',
            borderWidth: 2.5,
            pointBackgroundColor: '#0dcaf0',
            pointRadius: 4,
            fill: true,
            tension: 0.4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                grid: { color: gridColor },
                ticks: { font: { family: fontFamily, size: 11 }, stepSize: 1 },
                beginAtZero: true,
            },
            x: {
                grid: { display: false },
                ticks: { font: { family: fontFamily, size: 11 } }
            }
        }
    }
});

// ── 4. Grade Distribution Bar Chart ──────────────────────────
new Chart(document.getElementById('gradeChart'), {
    type: 'bar',
    data: {
        labels: @json($grades),
        datasets: [{
            label: 'Students',
            data:  @json($gradeCounts),
            backgroundColor: [
                'rgba(25,135,84,0.75)',   // A - success
                'rgba(13,110,253,0.75)',   // B - primary
                'rgba(13,202,240,0.75)',   // C - info
                'rgba(255,193,7,0.75)',    // D - warning
                'rgba(108,117,125,0.75)', // E - secondary
                'rgba(220,53,69,0.75)',   // F - danger
            ],
            borderRadius: 6,
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.raw + ' students'
                }
            }
        },
        scales: {
            y: {
                grid: { color: gridColor },
                ticks: { font: { family: fontFamily, size: 11 }, stepSize: 1 },
                beginAtZero: true,
            },
            x: {
                grid: { display: false },
                ticks: { font: { family: fontFamily, size: 11 } }
            }
        }
    }
});
</script>
@endpush

@endsection
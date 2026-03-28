@extends('layouts.proprietor')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid px-4">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-0">Proprietor Dashboard</h4>
                <small class="text-muted">
                    Welcome back, <strong>{{ auth()->user()->name }}</strong> &bull;
                    @if($currentSession && $currentTerm)
                        {{ $currentSession->name }} &mdash; {{ ucfirst($currentTerm->name) }} Term
                    @else
                        <span class="text-warning">No active session/term set</span>
                    @endif
                </small>
            </div>
            <a href="{{ route('proprietor.sessions.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-calendar3 me-1"></i> Manage Sessions
            </a>
        </div>

        {{-- No Session Warning --}}
        @if(!$currentSession || !$currentTerm)
            <div class="alert alert-warning d-flex align-items-center gap-2 rounded-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>
                    <strong>No active session or term set.</strong>
                    Features like fees, results, and payments depend on an active term.
                    <a href="{{ route('proprietor.sessions.index') }}" class="alert-link ms-1">Set one now &rarr;</a>
                </div>
            </div>
        @endif

        {{-- ── Stat Cards ── --}}
        <div class="row g-3 mb-4">

            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 bg-primary text-white bg-opacity-10 p-2">
                                <i class="bi bi-people-fill  fs-5"></i>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 rounded-pill" style="font-size:11px;">Active</span>
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
                            <div class="rounded-3 bg-success text-white bg-opacity-10 p-2">
                                <i class="bi bi-cash-stack fs-5"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 rounded-pill" style="font-size:11px;">This
                                Term</span>
                        </div>
                        <h3 class="fw-bold mb-0">₦{{ number_format($collected, 0) }}</h3>
                        <small class="text-muted">Fees Collected</small>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 bg-danger text-white bg-opacity-10 p-2">
                                <i class="bi bi-exclamation-circle-fill fs-5"></i>
                            </div>
                            <span class="badge bg-danger bg-opacity-10 rounded-pill" style="font-size:11px;">Owing</span>
                        </div>
                        <h3 class="fw-bold mb-0">{{ number_format($studentsOwing) }}</h3>
                        <small class="text-muted">Students Owing</small>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-3 bg-warning text-white bg-opacity-10 p-2">
                                <i class="bi bi-person-workspace fs-5"></i>
                            </div>
                            <span class="badge bg-warning bg-opacity-10 rounded-pill" style="font-size:11px;">Active</span>
                        </div>
                        <h3 class="fw-bold mb-0">{{ $totalStaff }}</h3>
                        <small class="text-muted">Staff & Admins</small>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Charts Row ── --}}
        <div class="row g-4 mb-4">

            {{-- Payment Growth Chart --}}
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
                        <h6 class="fw-semibold mb-0">Payment Collection — Last 6 Months</h6>
                        <span class="badge  bg-opacity-10 text-success rounded-pill" style="font-size:11px;">
                            <i class="bi bi-graph-up me-1"></i>Revenue
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="paymentChart" height="110"></canvas>
                    </div>
                </div>
            </div>

            {{-- Payment Status Donut --}}
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-semibold mb-0">Payment Status</h6>
                        <small class="text-muted">Current term breakdown</small>
                    </div>
                    <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                        <canvas id="statusChart" height="180"></canvas>
                        <div class="mt-3 w-100">
                            <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                                <span><span class="badge bg-success me-1">&nbsp;</span>Paid</span>
                                <strong>{{ $paymentBreakdown['paid'] ?? 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1" style="font-size:13px;">
                                <span><span class="badge bg-warning me-1">&nbsp;</span>Partial</span>
                                <strong>{{ $paymentBreakdown['partial'] ?? 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between" style="font-size:13px;">
                                <span><span class="badge bg-danger me-1">&nbsp;</span>Owing</span>
                                <strong>{{ $paymentBreakdown['owing'] ?? 0 }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Bottom Row ── --}}
        <div class="row g-4">

            {{-- Student Growth Chart --}}
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
                        <h6 class="fw-semibold mb-0">New Students — Last 6 Months</h6>
                        <span class="badge bg-opacity-10 text-primary rounded-pill" style="font-size:11px;">
                            <i class="bi bi-people me-1"></i>Enrolment
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="studentChart" height="160"></canvas>
                    </div>
                </div>
            </div>

            {{-- Recent Payments --}}
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
                        <h6 class="fw-semibold mb-0">Recent Payments</h6>
                        <a href="{{ route('admin.payments.index') }}" class="text-primary" style="font-size:13px;">View all
                            &rarr;</a>
                    </div>
                    <div class="card-body p-0">
                        @forelse($recentPayments as $payment)
                            <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                                @if($payment->student->photo)
                                    <img src="{{ asset('storage/' . $payment->student->photo) }}" class="rounded-circle"
                                        style="width:36px;height:36px;object-fit:cover;">
                                @else
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold
                                                                d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width:36px;height:36px;font-size:14px;">
                                        {{ strtoupper(substr($payment->student->first_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold text-truncate" style="font-size:14px;">
                                        {{ $payment->student->first_name ?? '' }} {{ $payment->student->last_name ?? '' }}
                                    </div>
                                    <small class="text-muted text-truncate d-block">
                                        {{ $payment->fee->feeType->name ?? 'Fee' }}
                                    </small>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <div class="fw-semibold text-success" style="font-size:14px;">
                                        ₦{{ number_format($payment->amount_paid, 0) }}
                                    </div>
                                    <small class="text-muted">{{ $payment->payment_date->format('d M') }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted" style="font-size:13px;">
                                <i class="bi bi-receipt fs-2 d-block opacity-25 mb-2"></i>
                                No payments recorded yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            // ── Data from Laravel ──
            const paymentLabels = @json($paymentGrowth->pluck('month'));
            const paymentData = @json($paymentGrowth->pluck('total'));
            const studentLabels = @json($studentGrowth->pluck('month'));
            const studentData = @json($studentGrowth->pluck('total'));
            const statusData = [
                {{ $paymentBreakdown['paid'] ?? 0 }},
                {{ $paymentBreakdown['partial'] ?? 0 }},
                {{ $paymentBreakdown['owing'] ?? 0 }}
            ];

            // ── Shared defaults ──
            Chart.defaults.font.family = 'inherit';
            Chart.defaults.color = '#64748b';

            // ── Payment Bar Chart ──
            new Chart(document.getElementById('paymentChart'), {
                type: 'bar',
                data: {
                    labels: paymentLabels,
                    datasets: [{
                        label: 'Amount Collected (₦)',
                        data: paymentData,
                        backgroundColor: 'rgba(59,130,246,0.15)',
                        borderColor: '#3b82f6',
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.04)' },
                            ticks: {
                                callback: v => '₦' + (v >= 1000 ? (v / 1000).toFixed(0) + 'k' : v)
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });

            // ── Status Donut Chart ──
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Paid', 'Partial', 'Owing'],
                    datasets: [{
                        data: statusData,
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` ${ctx.label}: ${ctx.parsed} students`
                            }
                        }
                    }
                }
            });

            // ── Student Line Chart ──
            new Chart(document.getElementById('studentChart'), {
                type: 'line',
                data: {
                    labels: studentLabels,
                    datasets: [{
                        label: 'New Students',
                        data: studentData,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139,92,246,0.08)',
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#8b5cf6',
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.04)' },
                            ticks: { stepSize: 1 }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        </script>
    @endpush

@endsection
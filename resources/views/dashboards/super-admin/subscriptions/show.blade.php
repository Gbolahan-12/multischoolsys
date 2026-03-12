@extends('layouts.superadmin')
@section('title', $school->name . ' — Subscriptions')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('superadmin.subscriptions.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="flex-grow-1">
            <h4 class="fw-bold mb-0">{{ $school->name }}</h4>
            <small class="text-muted">Subscription history & timeline</small>
        </div>
        <a href="{{ route('superadmin.subscriptions.create', ['school_id' => $school->id]) }}"
           class="btn btn-primary btn-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Record Payment
        </a>
    </div>

    {{-- ── Current Status Card ── --}}
    @if($latest)
    @php
        $isExpired  = now()->isAfter($latest->expires_at);
        $daysLeft   = $isExpired ? 0 : (int) now()->diffInDays($latest->expires_at);
        $cardBg     = match(true) {
            $isExpired     => '#fff5f5',
            $daysLeft <= 7 => '#fffbeb',
            default        => '#f0fdf4',
        };
        $cardBorder = match(true) {
            $isExpired     => '#fecaca',
            $daysLeft <= 7 => '#fde68a',
            default        => '#bbf7d0',
        };
        $statusText = match(true) {
            $isExpired     => 'Expired',
            $daysLeft <= 7 => "Expires in {$daysLeft} day(s) — renew soon!",
            default        => "Active — {$daysLeft} days remaining",
        };
        $statusColor = match(true) {
            $isExpired     => '#dc3545',
            $daysLeft <= 7 => '#d97706',
            default        => '#198754',
        };
        $totalPaid = $subscriptions->sum('amount');
        $totalMonths = $subscriptions->sum('duration_months');
    @endphp

    <div class="card border-0 shadow-sm rounded-3 mb-4"
         style="background:{{ $cardBg }};border:1px solid {{ $cardBorder }} !important;">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center text-center">
                <div class="col-6 col-md-3">
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Status</div>
                    <div class="fw-bold mt-1" style="font-size:15px;color:{{ $statusColor }};">
                        {{ $statusText }}
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Active Until</div>
                    <div class="fw-bold mt-1" style="font-size:15px;">
                        {{ $latest->expires_at->format('d M Y') }}
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Total Paid</div>
                    <div class="fw-bold mt-1 text-success" style="font-size:15px;">
                        ₦{{ number_format($totalPaid, 2) }}
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Total Duration</div>
                    <div class="fw-bold mt-1" style="font-size:15px;">
                        {{ $totalMonths }} month(s)
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4">

        {{-- ── Left: Timeline ── --}}
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold"
                        style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                        <i class="bi bi-bar-chart-steps me-2 text-primary"></i>Subscription Timeline
                    </h6>
                </div>
                <div class="card-body p-4">
                    @if($subscriptions->count())

                    {{-- Overall bar --}}
                    @php
                        $first      = $subscriptions->last(); // oldest
                        $last       = $subscriptions->first(); // newest
                        $totalSpan  = $first->starts_at->diffInDays($last->expires_at);
                    @endphp

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1" style="font-size:11px;color:#777;">
                            <span>{{ $first->starts_at->format('d M Y') }}</span>
                            <span>{{ $last->expires_at->format('d M Y') }}</span>
                        </div>
                        <div class="rounded-pill overflow-hidden" style="height:10px;background:#e9ecef;">
                            @php
                                $elapsed    = min($totalSpan, $first->starts_at->diffInDays(now()));
                                $pct        = $totalSpan > 0 ? min(100, round(($elapsed / $totalSpan) * 100)) : 0;
                                $barColor   = $pct >= 100 ? '#dc3545' : ($pct >= 80 ? '#f59f00' : '#198754');
                            @endphp
                            <div style="width:{{ $pct }}%;height:100%;background:{{ $barColor }};transition:width .5s;"></div>
                        </div>
                        <div class="text-center mt-1" style="font-size:11px;color:#777;">
                            {{ $pct }}% of total subscription used
                        </div>
                    </div>

                    {{-- Per-payment timeline blocks --}}
                    <div class="position-relative">
                        {{-- Vertical line --}}
                        <div class="position-absolute"
                             style="left:15px;top:0;bottom:0;width:2px;background:#e9ecef;"></div>

                        @foreach($subscriptions->sortBy('starts_at') as $i => $sub)
                        @php
                            $subIsExpired = now()->isAfter($sub->expires_at);
                            $subIsCurrent = now()->between($sub->starts_at, $sub->expires_at);
                            $dotColor = match(true) {
                                $subIsCurrent => '#198754',
                                $subIsExpired => '#adb5bd',
                                default       => '#0d6efd',
                            };
                            $mc = ['cash'=>'success','transfer'=>'primary','pos'=>'info','cheque'=>'secondary'];
                        @endphp

                        <div class="d-flex gap-3 mb-4 position-relative">
                            {{-- Dot --}}
                            <div class="rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center"
                                 style="width:30px;height:30px;background:{{ $dotColor }};
                                        z-index:1;margin-top:2px;flex-shrink:0;">
                                <span style="color:#fff;font-size:11px;font-weight:700;">{{ $i + 1 }}</span>
                            </div>

                            {{-- Content --}}
                            <div class="flex-grow-1 rounded-3 p-3"
                                 style="background:{{ $subIsCurrent ? '#f0fdf4' : ($subIsExpired ? '#f8f9fa' : '#eff6ff') }};
                                        border:1px solid {{ $subIsCurrent ? '#bbf7d0' : ($subIsExpired ? '#dee2e6' : '#bfdbfe') }};">

                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fw-bold" style="font-size:13px;">
                                        {{ $sub->duration_label }}
                                        @if($subIsCurrent)
                                        <span class="badge bg-success-subtle text-success rounded-pill ms-1"
                                              style="font-size:10px;">Current</span>
                                        @elseif($subIsExpired)
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1"
                                              style="font-size:10px;">Elapsed</span>
                                        @else
                                        <span class="badge bg-primary-subtle text-primary rounded-pill ms-1"
                                              style="font-size:10px;">Upcoming</span>
                                        @endif
                                    </span>
                                    <span class="fw-bold text-success" style="font-size:13px;">
                                        ₦{{ number_format($sub->amount, 2) }}
                                    </span>
                                </div>

                                {{-- Date range --}}
                                <div class="d-flex align-items-center gap-2 mb-2" style="font-size:12px;color:#555;">
                                    <i class="bi bi-calendar3"></i>
                                    <span>{{ $sub->starts_at->format('d M Y') }}</span>
                                    <i class="bi bi-arrow-right"></i>
                                    <span class="{{ $subIsExpired ? 'text-danger' : '' }}">
                                        {{ $sub->expires_at->format('d M Y') }}
                                    </span>
                                </div>
                                @if($subIsCurrent)
                                @php
                                    $subTotal   = $sub->starts_at->diffInDays($sub->expires_at);
                                    $subElapsed = $sub->starts_at->diffInDays(now());
                                    $subPct     = $subTotal > 0 ? min(100, round(($subElapsed / $subTotal) * 100)) : 0;
                                    $subDaysLeft = (int) now()->diffInDays($sub->expires_at);
                                @endphp
                                <div class="rounded-pill overflow-hidden mb-1" style="height:5px;background:#d1fae5;">
                                    <div style="width:{{ $subPct }}%;height:100%;background:#198754;"></div>
                                </div>
                                <div style="font-size:11px;color:#059669;">
                                    {{ $subDaysLeft }} days remaining in this plan
                                </div>
                                @endif

                                {{-- Method + payment date --}}
                                <div class="d-flex align-items-center gap-2 mt-2" style="font-size:11px;color:#777;">
                                    <span class="badge bg-{{ $mc[$sub->payment_method] ?? 'secondary' }}-subtle
                                                 text-{{ $mc[$sub->payment_method] ?? 'secondary' }} rounded-pill">
                                        {{ ucfirst($sub->payment_method) }}
                                    </span>
                                    <span>Paid: {{ $sub->payment_date->format('d M Y') }}</span>
                                    @if($sub->reference)
                                    <span>· Ref: {{ $sub->reference }}</span>
                                    @endif
                                </div>

                                @if($sub->note)
                                <div class="mt-1" style="font-size:11px;color:#777;font-style:italic;">
                                    "{{ $sub->note }}"
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach

                        {{-- End marker --}}
                        @if($latest)
                        <div class="d-flex gap-3 position-relative">
                            <div class="rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center"
                                 style="width:30px;height:30px;background:{{ now()->isAfter($latest->expires_at) ? '#dc3545' : '#0d6efd' }};z-index:1;">
                                <i class="bi bi-flag-fill" style="color:#fff;font-size:11px;"></i>
                            </div>
                            <div class="d-flex align-items-center" style="font-size:13px;">
                                <span class="fw-bold {{ now()->isAfter($latest->expires_at) ? 'text-danger' : 'text-primary' }}">
                                    Subscription ends: {{ $latest->expires_at->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                        @endif
                    </div>

                    @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-clock-history d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
                        No payments recorded yet.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Right: Payment Table ── --}}
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold"
                        style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                        <i class="bi bi-receipt me-2 text-primary"></i>Payment Records
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($subscriptions->count())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3">#</th>
                                    <th class="py-3 text-end">Amount</th>
                                    <th class="py-3 text-center">Plan</th>
                                    <th class="py-3">Starts</th>
                                    <th class="py-3">Expires</th>
                                    <th class="py-3">Method</th>
                                    <th class="py-3">Recorded By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptions->sortBy('starts_at') as $i => $sub)
                                @php
                                    $subIsExpired = now()->isAfter($sub->expires_at);
                                    $subIsCurrent = now()->between($sub->starts_at, $sub->expires_at);
                                    $mc = ['cash'=>'success','transfer'=>'primary','pos'=>'info','cheque'=>'secondary'];
                                @endphp
                                <tr class="{{ $subIsCurrent ? 'table-success' : '' }}">
                                    <td class="ps-4 text-muted">{{ $i + 1 }}</td>
                                    <td class="text-end fw-semibold text-success">
                                        ₦{{ number_format($sub->amount, 2) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary rounded-pill">
                                            {{ $sub->duration_label }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $sub->starts_at->format('d M Y') }}</td>
                                    <td class="{{ $subIsExpired ? 'text-danger fw-semibold' : 'text-muted' }}">
                                        {{ $sub->expires_at->format('d M Y') }}
                                        @if($subIsCurrent)
                                        <span class="badge bg-success-subtle text-success rounded-pill ms-1"
                                              style="font-size:10px;">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $mc[$sub->payment_method] ?? 'secondary' }}-subtle
                                                     text-{{ $mc[$sub->payment_method] ?? 'secondary' }} rounded-pill"
                                              style="font-size:11px;">
                                            {{ ucfirst($sub->payment_method) }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $sub->recordedBy->name ?? '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td class="ps-4 fw-bold">Total</td>
                                    <td class="text-end fw-bold text-success">
                                        ₦{{ number_format($subscriptions->sum('amount'), 2) }}
                                    </td>
                                    <td class="text-center fw-bold">
                                        {{ $subscriptions->sum('duration_months') }} months
                                    </td>
                                    <td colspan="4"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-receipt d-block mb-2" style="font-size:2.5rem;opacity:.3;"></i>
                        No payments recorded yet.
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
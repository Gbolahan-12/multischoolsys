@extends('layouts.superadmin')
@section('title', 'Subscriptions')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">Subscriptions</h4>
            <small class="text-muted">Manage school subscription payments</small>
        </div>
        <a href="{{ route('superadmin.subscriptions.create') }}" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Record Payment
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── Warning Banners ── --}}
    @if($inWarning->count())
    <div class="alert rounded-3 mb-4 d-flex align-items-start gap-2"
         style="background:#fff3cd;border:1px solid #ffc107;">
        <i class="bi bi-exclamation-triangle-fill text-warning mt-1 flex-shrink-0"></i>
        <div>
            <strong>{{ $inWarning->count() }} school(s) in grace period</strong> — subscription expired,
            will be suspended in {{ App\Services\SubscriptionService::GRACE_PERIOD_DAYS }} days if not renewed:
            <strong>{{ $inWarning->pluck('name')->join(', ') }}</strong>
        </div>
    </div>
    @endif

    @if($expiringSoon->count())
    <div class="alert rounded-3 mb-4 d-flex align-items-start gap-2"
         style="background:#fff8e1;border:1px solid #ffe082;">
        <i class="bi bi-clock-history text-warning mt-1 flex-shrink-0"></i>
        <div style="font-size:13px;">
            <strong>Expiring within 7 days:</strong>
            @foreach($expiringSoon as $s)
            <span class="badge bg-warning-subtle text-warning rounded-pill mx-1">
                {{ $s->name }} — {{ $s->subscription_expires_at->format('d M Y') }}
            </span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Filters ── --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold mb-1" style="font-size:12px;">School</label>
                        <select name="school_id" class="form-select form-select-sm"
                                onchange="this.form.submit()">
                            <option value="">All Schools</option>
                            @foreach($schools as $school)
                            <option value="{{ $school->id }}"
                                {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold mb-1" style="font-size:12px;">Search</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   class="form-control" placeholder="Search school name...">
                            <button class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                            @if(request()->hasAny(['search','school_id']))
                            <a href="{{ route('superadmin.subscriptions.index') }}"
                               class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Subscriptions Table ── --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-semibold"
                style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                Payment History
            </h6>
            @if($subscriptions->total())
            <span class="badge bg-primary rounded-pill">{{ $subscriptions->total() }} records</span>
            @endif
        </div>
        <div class="card-body p-0">
            @if($subscriptions->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">School</th>
                            <th class="py-3 text-end">Amount</th>
                            <th class="py-3 text-center">Duration</th>
                            <th class="py-3">Starts</th>
                            <th class="py-3">Expires</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3">Method</th>
                            <th class="py-3">Recorded By</th>
                            <th class="py-3">Payment Date</th>
                            <th class="py-3 pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscriptions as $sub)
                        @php
                            $isExpired  = now()->isAfter($sub->expires_at);
                            $daysLeft   = $isExpired ? 0 : (int) now()->diffInDays($sub->expires_at);
                            $statusColor = match(true) {
                                $isExpired         => 'danger',
                                $daysLeft <= 7     => 'warning',
                                default            => 'success',
                            };
                            $statusLabel = match(true) {
                                $isExpired         => 'Expired',
                                $daysLeft <= 7     => "Expires in {$daysLeft}d",
                                default            => 'Active',
                            };
                            $methodColors = ['cash'=>'success','transfer'=>'primary','pos'=>'info','cheque'=>'secondary'];
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $sub->school->name }}</div>
                            </td>
                            <td class="text-end fw-semibold text-success">
                                ₦{{ number_format($sub->amount, 2) }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary rounded-pill">
                                    {{ $sub->duration_label }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $sub->starts_at->format('d M Y') }}</td>
                            <td class="{{ $isExpired ? 'text-danger fw-semibold' : 'text-muted' }}">
                                {{ $sub->expires_at->format('d M Y') }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} rounded-pill"
                                      style="font-size:11px;">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $methodColors[$sub->payment_method] ?? 'secondary' }}-subtle
                                             text-{{ $methodColors[$sub->payment_method] ?? 'secondary' }} rounded-pill"
                                      style="font-size:11px;">
                                    {{ ucfirst($sub->payment_method) }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $sub->recordedBy->fullname ?? '—' }}</td>
                            <td class="text-muted">{{ $sub->payment_date->format('d M Y') }}</td>
                            <td class="pe-4">
                                <a href="{{ route('superadmin.subscriptions.show', $sub->school) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($subscriptions->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $subscriptions->links('pagination::bootstrap-5') }}
            </div>
            @endif

            @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-receipt d-block mb-2" style="font-size:2.5rem;opacity:.3;"></i>
                <p class="mb-0">No subscription payments recorded yet.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
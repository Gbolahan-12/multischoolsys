@extends('layouts.admin')
@section('title', 'Payments')
@section('content')
<div class="container-fluid px-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Payments</h4>
            <small class="text-muted">
                @if($currentSession && $currentTerm)
                    {{ $currentSession->name }} &mdash; {{ ucfirst($currentTerm->name) }} Term
                @else
                    <span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>No active term</span>
                @endif
            </small>
        </div>
        <a href="{{ route('admin.payments.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Record Payment
        </a>
    </div>

    {{-- Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <small class="text-muted d-block mb-1">Collected</small>
                    <h5 class="fw-bold mb-0 text-success">₦{{ number_format($summary['total_collected'], 0) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <small class="text-muted d-block mb-1">Outstanding</small>
                    <h5 class="fw-bold mb-0 text-danger">₦{{ number_format($summary['total_outstanding'], 0) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <small class="text-muted d-block mb-1">Fully Paid</small>
                    <h5 class="fw-bold mb-0 text-primary">{{ $summary['paid_count'] }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <small class="text-muted d-block mb-1">Owing / Partial</small>
                    <h5 class="fw-bold mb-0 text-warning">{{ $summary['owing_count'] }}</h5>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-sm-4">
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control form-control-sm" placeholder="Search student...">
                    </div>
                    <div class="col-12 col-sm-3">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="paid"    {{ request('status') === 'paid'    ? 'selected' : '' }}>Paid</option>
                            <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="owing"   {{ request('status') === 'owing'   ? 'selected' : '' }}>Owing</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-3">
                        <select name="class_id" class="form-select form-select-sm">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Payments Table --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3" style="font-size:12px;">Student</th>
                            <th class="py-3 d-none d-md-table-cell" style="font-size:12px;">Fee</th>
                            <th class="py-3" style="font-size:12px;">Paid</th>
                            <th class="py-3 d-none d-sm-table-cell" style="font-size:12px;">Balance</th>
                            <th class="py-3" style="font-size:12px;">Status</th>
                            <th class="py-3 d-none d-lg-table-cell" style="font-size:12px;">Date</th>
                            <th class="py-3 pe-4 text-end" style="font-size:12px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold" style="font-size:14px;">
                                    <a href="{{ route('admin.payments.show', $payment->student) }}"
                                       class="text-decoration-none text-dark">
                                        {{ $payment->student->short_name }}
                                    </a>
                                </div>
                                <small class="text-muted">{{ $payment->student->admission_number }}</small>
                            </td>
                            <td class="d-none d-md-table-cell" style="font-size:13px;">
                                {{ $payment->fee->feeType->name }}
                                @if($payment->fee->schoolClass)
                                    <small class="text-muted d-block">{{ $payment->fee->schoolClass->full_name }}</small>
                                @endif
                            </td>
                            <td class="fw-semibold text-success" style="font-size:14px;">
                                ₦{{ number_format($payment->amount_paid, 0) }}
                            </td>
                            <td class="d-none d-sm-table-cell text-danger" style="font-size:13px;">
                                ₦{{ number_format($payment->balance, 0) }}
                            </td>
                            <td>
                                @if($payment->status === 'paid')
                                    <span class="badge bg-success rounded-pill">Paid</span>
                                @elseif($payment->status === 'partial')
                                    <span class="badge bg-warning text-dark rounded-pill">Partial</span>
                                @else
                                    <span class="badge bg-danger rounded-pill">Owing</span>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell text-muted" style="font-size:13px;">
                                {{ $payment->payment_date->format('d M Y') }}
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('admin.payments.show', $payment->student) }}"
                                       class="" title="View Student">
                                        <i data-feather="eye"></i></span>
                                    </a>
                                    <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm align-center justify-content-center relative" style="width:15px; height: 15px;"
                                                onclick="return confirm('Delete this payment record?')">
                                            <i data-feather="trash" class="absolute" style="width:15px;"></i></span>
                                        </button>
                                        {{-- <a href="#" type="submit" onclick="return confirm('Delete this payment record?')"><span class="icon"><i data-feather="trash" class="text-danger"></i></span></a> --}}
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt fs-1 d-block opacity-25 mb-3"></i>
                                No payments recorded yet.
                                <a href="{{ route('admin.payments.create') }}">Record the first one.</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $payments->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
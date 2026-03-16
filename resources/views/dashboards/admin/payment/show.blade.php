@extends('layouts.admin')
@section('title', 'Student Payments')
@section('content')
    <div class="container-fluid px-4">

        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-0">{{ $student->full_name }}</h4>
                <small class="text-muted">{{ $student->admission_number }} &bull; Payment History</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.payments.create', ['student_id' => $student->id]) }}"
                    class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Record Payment
                </a>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="row g-4">

            {{-- Left: Fee Status for current term --}}
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                            <i class="bi bi-clipboard-check me-2 text-primary"></i>
                            Current Term Fees
                            @if($currentTerm) <span class="text-primary">— {{ ucfirst($currentTerm->name) }}</span> @endif
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        @forelse($termFees as $fee)
                            @php
                                $payment = $payments->firstWhere('fee_id', $fee->id);
                            @endphp
                            <div class="px-4 py-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="fw-semibold" style="font-size:14px;">{{ $fee->feeType->name }}</span>
                                    @if(!$payment)
                                        <span class="badge bg-danger rounded-pill">Owing</span>
                                    @elseif($payment->status === 'paid')
                                        <span class="badge bg-success rounded-pill">Paid</span>
                                    @elseif($payment->status === 'partial')
                                        <span class="badge bg-warning text-dark rounded-pill">Partial</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill">Owing</span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between" style="font-size:12px;">
                                    <span class="text-muted">₦{{ number_format($fee->amount, 0) }} total</span>
                                    <span class="text-success">₦{{ number_format($payment?->amount_paid ?? 0, 0) }} paid</span>
                                </div>
                                @if($payment && $payment->balance > 0)
                                    <div class="text-danger mt-1" style="font-size:12px;">
                                        Balance: ₦{{ number_format($payment->balance, 0) }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-muted py-4" style="font-size:13px;">
                                <i class="bi bi-info-circle d-block mb-2 opacity-50"></i>
                                No fees assigned for current term.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right: All Payments --}}
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                            <i class="bi bi-clock-history me-2 text-primary"></i>All Payments
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4 py-2" style="font-size:12px;">Fee</th>
                                        <th class="py-2" style="font-size:12px;">Amount</th>
                                        <th class="py-2 d-none d-sm-table-cell" style="font-size:12px;">Balance</th>
                                        <th class="py-2" style="font-size:12px;">Status</th>
                                        <th class="py-2 d-none d-md-table-cell" style="font-size:12px;">Date</th>
                                        <th class="py-2 pe-4 text-end" style="font-size:12px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $payment)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-semibold" style="font-size:14px;">
                                                    {{ $payment->fee->feeType->name }}
                                                </div>
                                                <small class="text-muted">
                                                    {{ $payment->fee->term ? ucfirst($payment->fee->term->name) . ' Term' : '' }}
                                                    {{ $payment->fee->session?->name ? '— ' . $payment->fee->session->name : '' }}
                                                </small>
                                            </td>
                                            <td class="text-success fw-semibold" style="font-size:14px;">
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
                                            <td class="d-none d-md-table-cell text-muted" style="font-size:13px;">
                                                {{ $payment->payment_date->format('d M Y') }}
                                                @if($payment->recordedBy)
                                                    <small class="d-block">by {{ $payment->recordedBy->fullname }}</small>
                                                @endif
                                            </td>
                                            <td class="pe-4 text-end">
                                                <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        onclick="return confirm('Delete this payment record?')"
                                                        class="btn btn-link p-0 border-0">
                                                        <span class="icon">
                                                            <i data-feather="trash" class="text-danger"></i>
                                                        </span>
                                                    </button>

                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted" style="font-size:13px;">
                                                <i class="bi bi-receipt fs-2 d-block opacity-25 mb-2"></i>
                                                No payments recorded yet.
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
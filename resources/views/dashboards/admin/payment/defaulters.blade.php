@extends('layouts.admin')
@section('title', 'Defaulter Payments')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">Defaulter Payments</h4>
            <small class="text-muted">Optional/one-time fee payments e.g. Uniform, Excursion</small>
        </div>
        <a href="{{ route('admin.payments.defaulter.create') }}" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Record Payment
        </a>
    </div>


    {{-- ── Filters ── --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" id="filterForm">
                <div class="row g-2 align-items-end">

                    {{-- Session --}}
                    <div class="col-12 col-md-2">
                        <label class="form-label fw-semibold mb-1" style="font-size:12px;">Session</label>
                        <select name="session_id" id="sessionSelect" class="form-select form-select-sm">
                            <option value="">All Sessions</option>
                            @foreach($sessions as $session)
                            <option value="{{ $session->id }}"
                                {{ $selectedSessionId == $session->id ? 'selected' : '' }}>
                                {{ $session->name }}
                                @if($session->is_current) (Current) @endif
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Term --}}
                    <div class="col-12 col-md-2">
                        <label class="form-label fw-semibold mb-1" style="font-size:12px;">Term</label>
                        <select name="term_id" id="termSelect" class="form-select form-select-sm">
                            <option value="">All Terms</option>
                            @foreach($terms as $term)
                            <option value="{{ $term->id }}"
                                {{ $selectedTermId == $term->id ? 'selected' : '' }}>
                                {{ ucfirst($term->name) }} Term
                                @if($term->is_current) (Current) @endif
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Fee --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold mb-1" style="font-size:12px;">Defaulter Fee</label>
                        <select name="fee_id" id="feeSelect" class="form-select form-select-sm">
                            <option value="">All Defaulter Fees</option>
                            @foreach($fees as $fee)
                            <option value="{{ $fee->id }}"
                                {{ $selectedFeeId == $fee->id ? 'selected' : '' }}>
                                {{ $fee->feeType->name }} — ₦{{ number_format($fee->amount, 2) }}
                                {{ $fee->schoolClass ? '(' . $fee->schoolClass->name . ')' : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="col-12 col-md-2">
                        <label class="form-label fw-semibold mb-1" style="font-size:12px;">Status</label>
                        <select name="status" class="form-select form-select-sm"
                                onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Statuses</option>
                            @foreach(['paid' => 'Paid', 'partial' => 'Partial', 'owing' => 'Owing'] as $val => $label)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold mb-1" style="font-size:12px;">Search</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search"
                                   value="{{ request('search') }}"
                                   class="form-control"
                                   placeholder="Name or admission no...">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                            @if(request()->hasAny(['search','fee_id','status','term_id','session_id']))
                            <a href="{{ route('admin.payments.defaulter.index') }}"
                               class="btn btn-outline-secondary" title="Reset filters">
                                <i class="bi bi-x-lg"></i>
                            </a>
                            @endif
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    @if($summary)

    {{-- ── Summary Cards ── --}}
    <div class="row g-3 mb-4">
        @php
            $cards = [
                ['label' => 'Total Records',   'value' => $summary['total'],                              'icon' => 'bi-list-ul',        'color' => '#0d6efd'],
                ['label' => 'Fully Paid',      'value' => $summary['paid'],                               'icon' => 'bi-check-circle',   'color' => '#198754'],
                ['label' => 'Partial',         'value' => $summary['partial'],                            'icon' => 'bi-hourglass-split','color' => '#fd7e14'],
                ['label' => 'Owing',           'value' => $summary['owing'],                              'icon' => 'bi-exclamation-circle','color' => '#dc3545'],
                ['label' => 'Amount Collected','value' => '₦' . number_format($summary['total_amount'],2),'icon' => 'bi-cash-stack',     'color' => '#6f42c1'],
            ];
        @endphp
        @foreach($cards as $card)
        <div class="col-6 col-lg">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3">
                    <div class="rounded-2 d-inline-flex align-items-center justify-content-center mb-2"
                         style="width:34px;height:34px;background:{{ $card['color'] }}1a;">
                        <i class="{{ $card['icon'] }}" style="color:{{ $card['color'] }};font-size:15px;"></i>
                    </div>
                    <div class="fw-bold" style="font-size:1.3rem;line-height:1.2;">{{ $card['value'] }}</div>
                    <div class="text-muted" style="font-size:11px;">{{ $card['label'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Table ── --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-semibold" style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                <i class="bi bi-receipt me-2 text-primary"></i>Defaulter Payment Records
            </h6>
            @if($payments->total() > 0)
            <span class="badge bg-primary rounded-pill">{{ $payments->total() }} records</span>
            @endif
        </div>

        <div class="card-body p-0">
            @if($payments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">#</th>
                            <th class="py-3">Student</th>
                            <th class="py-3">Admission No.</th>
                            <th class="py-3">Fee</th>
                            <th class="py-3">Class</th>
                            <th class="py-3 text-end">Fee Amount</th>
                            <th class="py-3 text-end">Amount Paid</th>
                            <th class="py-3 text-end">Balance</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3">Method</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Received By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $i => $payment)
                        @php
                            $statusColors = ['paid' => 'success', 'partial' => 'warning', 'owing' => 'danger'];
                            $methodColors = ['cash' => 'success', 'transfer' => 'primary', 'pos' => 'info', 'cheque' => 'secondary'];
                            $sc = $statusColors[$payment->status] ?? 'secondary';
                            $mc = $methodColors[$payment->payment_method] ?? 'secondary';
                        @endphp
                        <tr>
                            <td class="ps-4 text-muted">
                                {{ ($payments->currentPage() - 1) * $payments->perPage() + $i + 1 }}
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning fw-bold
                                                d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:32px;height:32px;font-size:12px;">
                                        {{ strtoupper(substr($payment->student->first_name, 0, 1)) }}
                                    </div>
                                    <div class="fw-semibold">
                                        {{ $payment->student->last_name }},
                                        {{ $payment->student->first_name }}
                                    </div>
                                </div>
                            </td>

                            <td class="text-muted">{{ $payment->student->admission_number }}</td>

                            <td>
                                <div class="fw-semibold">{{ $payment->fee->feeType->name }}</div>
                                <small class="text-muted">
                                    <span class="badge bg-warning-subtle text-warning rounded-pill" style="font-size:10px;">
                                        Optional
                                    </span>
                                </small>
                            </td>

                            <td class="text-muted">
                                {{ $payment->fee->schoolClass->name ?? 'All Classes' }}
                            </td>

                            <td class="text-end text-muted">
                                ₦{{ number_format($payment->fee->amount, 2) }}
                            </td>

                            <td class="text-end fw-semibold text-success">
                                ₦{{ number_format($payment->amount_paid, 2) }}
                            </td>

                            <td class="text-end {{ $payment->balance > 0 ? 'text-danger' : 'text-muted' }}">
                                ₦{{ number_format($payment->balance, 2) }}
                            </td>

                            <td class="text-center">
                                <span class="badge bg-{{ $sc }}-subtle text-{{ $sc }} rounded-pill"
                                      style="font-size:11px;">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-{{ $mc }}-subtle text-{{ $mc }} rounded-pill"
                                      style="font-size:11px;">
                                    {{ ucfirst($payment->payment_method) }}
                                </span>
                            </td>

                            <td class="text-muted">
                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                            </td>

                            <td class="text-muted">{{ $payment->receivedBy->name ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $payments->links('pagination::bootstrap-5') }}
            </div>
            @endif

            @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox d-block mb-2" style="font-size:2.5rem;opacity:.3;"></i>
                <p class="mb-0 fw-semibold">No defaulter payments found.</p>
                <small>Try adjusting your filters or record a new payment.</small>
            </div>
            @endif
        </div>
    </div>

    @else
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-filter d-block mb-3" style="font-size:2.5rem;opacity:.3;"></i>
            <p class="mb-0 fw-semibold">Select a session and term to view defaulter payments</p>
            <small>Use the filters above to get started.</small>
        </div>
    </div>
    @endif

</div>

<script>
const termsBySessionUrl     = "{{ route('admin.payments.terms') }}";
const defaulterFeesByTermUrl = "{{ route('admin.payments.defaulter.fees') }}";

document.getElementById('sessionSelect').addEventListener('change', function () {
    const termSelect = document.getElementById('termSelect');
    const feeSelect  = document.getElementById('feeSelect');
    termSelect.innerHTML = '<option value="">Loading...</option>';
    feeSelect.innerHTML  = '<option value="">All Defaulter Fees</option>';

    if (!this.value) {
        termSelect.innerHTML = '<option value="">All Terms</option>';
        return;
    }

    fetch(`${termsBySessionUrl}?session_id=${this.value}`)
        .then(r => r.json())
        .then(terms => {
            termSelect.innerHTML = '<option value="">All Terms</option>';
            terms.forEach(t => {
                termSelect.innerHTML += `<option value="${t.id}">
                    ${t.name.charAt(0).toUpperCase() + t.name.slice(1)} Term
                    ${t.is_current ? '(Current)' : ''}
                </option>`;
            });
            document.getElementById('filterForm').submit();
        });
});

document.getElementById('termSelect').addEventListener('change', function () {
    const feeSelect = document.getElementById('feeSelect');
    feeSelect.innerHTML = '<option value="">Loading...</option>';

    if (!this.value) {
        feeSelect.innerHTML = '<option value="">All Defaulter Fees</option>';
        document.getElementById('filterForm').submit();
        return;
    }

    fetch(`${defaulterFeesByTermUrl}?term_id=${this.value}`)
        .then(r => r.json())
        .then(fees => {
            feeSelect.innerHTML = '<option value="">All Defaulter Fees</option>';
            fees.forEach(f => {
                feeSelect.innerHTML += `<option value="${f.id}">${f.name}</option>`;
            });
            document.getElementById('filterForm').submit();
        });
});

document.getElementById('feeSelect').addEventListener('change', function () {
    document.getElementById('filterForm').submit();
});
</script>
@endsection
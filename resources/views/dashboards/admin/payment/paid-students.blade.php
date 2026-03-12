@extends('layouts.admin')
@section('title', 'Paid Students')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">Paid Students</h4>
            <small class="text-muted">Students who have completed payment for a fee</small>
        </div>
    </div>

    {{-- ── Filters ── --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" id="filterForm">
                <div class="row g-2 align-items-end">

                    {{-- Session --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold mb-1" style="font-size:12px;">
                            Session
                        </label>
                        <select name="session_id" id="sessionSelect" class="form-select form-select-sm">
                            <option value="">Select Session</option>
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
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold mb-1" style="font-size:12px;">
                            Term
                        </label>
                        <select name="term_id" id="termSelect" class="form-select form-select-sm">
                            <option value="">Select Term</option>
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
                        <label class="form-label fw-semibold mb-1" style="font-size:12px;">
                            Fee
                        </label>
                        <select name="fee_id" id="feeSelect" class="form-select form-select-sm">
                            <option value="">All Fees</option>
                            @foreach($fees as $fee)
                            <option value="{{ $fee->id }}"
                                {{ $selectedFeeId == $fee->id ? 'selected' : '' }}>
                                {{ $fee->feeType->name }} — ₦{{ number_format($fee->amount, 2) }}
                                {{ $fee->class ? '(' . $fee->class->name . ')' : '(All Classes)' }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold mb-1" style="font-size:12px;">
                            Search Student
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search"
                                   value="{{ request('search') }}"
                                   class="form-control"
                                   placeholder="Name or admission no...">
                            <button class="btn btn-primary" type="submit">
                                <i class="feather-search"></i>
                            </button>
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
                ['label' => 'Total Paid',      'value' => $summary['total_paid'],                         'icon' => 'users',        'color' => '#0d6efd'],
                ['label' => 'Total Amount',    'value' => '₦' . number_format($summary['total_amount'],2), 'icon' => 'dollar-sign',  'color' => '#198754'],
                ['label' => 'Cash Payments',   'value' => $summary['cash'],                               'icon' => 'credit-card',  'color' => '#fd7e14'],
                ['label' => 'Bank Transfer',   'value' => $summary['transfer'],                           'icon' => 'smartphone',   'color' => '#6f42c1'],
                ['label' => 'POS Payments',    'value' => $summary['pos'],                                'icon' => 'activity',     'color' => '#0dcaf0'],
            ];
        @endphp
        @foreach($cards as $card)
        <div class="col-6 col-lg">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center"
                             style="width:34px;height:34px;background:{{ $card['color'] }}1a;">
                            <i class="{{ $card['icon'] }}" data-feather="{{ $card['icon'] }}" style="color:{{ $card['color'] }}; font-size:15px;"></i>
                        </div>
                    </div>
                    <div class="fw-bold" style="font-size:1.3rem;line-height:1.2;">
                        {{ $card['value'] }}
                    </div>
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
                <i class="feather-check-circle me-2 text-success"></i>Paid Students
            </h6>
            @if($payments->total() > 0)
            <span class="badge bg-success rounded-pill">{{ $payments->total() }} records</span>
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
                            <th class="py-3 text-end">Amount Paid</th>
                            <th class="py-3">Method</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Received By</th>
                            <th class="py-3">Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $i => $payment)
                        <tr>
                            <td class="ps-4 text-muted">
                                {{ ($payments->currentPage() - 1) * $payments->perPage() + $i + 1 }}
                            </td>

                            {{-- Student --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:32px;height:32px;font-size:12px;">
                                        {{ strtoupper(substr($payment->student->first_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">
                                            {{ $payment->student->last_name }},
                                            {{ $payment->student->first_name }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Admission No --}}
                            <td class="text-muted">{{ $payment->student->admission_number }}</td>

                            {{-- Fee --}}
                            <td>
                                <div class="fw-semibold">{{ $payment->fee->feeType->name }}</div>
                            </td>

                            {{-- Class --}}
                            <td class="text-muted">
                                {{ $payment->fee->class->name ?? 'All Classes' }}
                            </td>

                            {{-- Amount --}}
                            <td class="text-end fw-semibold text-success">
                                ₦{{ number_format($payment->amount_paid, 2) }}
                            </td>

                            {{-- Method --}}
                            <td>
                                @php
                                    $methodColors = [
                                        'cash'     => 'success',
                                        'transfer' => 'primary',
                                        'pos'      => 'info',
                                        'cheque'   => 'secondary',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $methodColors[$payment->payment_method] ?? 'secondary' }}-subtle
                                             text-{{ $methodColors[$payment->payment_method] ?? 'secondary' }}
                                             rounded-pill" style="font-size:11px;">
                                    {{ ucfirst($payment->payment_method) }}
                                </span>
                            </td>

                            {{-- Date --}}
                            <td class="text-muted">
                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                            </td>

                            {{-- Received By --}}
                            <td class="text-muted">
                                {{ $payment->receivedBy->fullname ?? '—' }}
                            </td>

                            {{-- Reference --}}
                            <td class="text-muted" style="font-size:12px;">
                                {{ $payment->reference ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($payments->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $payments->links('pagination::bootstrap-5') }}
            </div>
            @endif

            @else
            <div class="text-center py-5 text-muted">
                <i class="feather-inbox d-block mb-2" style="font-size:2.5rem;opacity:.3;"></i>
                <p class="mb-0">No paid students found for the selected filters.</p>
                <small>Try selecting a different term or fee.</small>
            </div>
            @endif
        </div>
    </div>

    @else
    {{-- No term selected yet --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body text-center py-5 text-muted">
            <i class="feather-filter d-block mb-3" style="font-size:2.5rem;opacity:.3;"></i>
            <p class="mb-0 fw-semibold">Select a session and term to view paid students</p>
            <small>Use the filters above to get started.</small>
        </div>
    </div>
    @endif

</div>

<script>
    const routes = {
        termsBySession: "{{ route('admin.paid-students.terms') }}",
        feesByTerm:     "{{ route('admin.paid-students.fees') }}",
    };

    // When session changes → reload terms via AJAX, reset fee
    document.getElementById('sessionSelect').addEventListener('change', function () {
        const sessionId  = this.value;
        const termSelect = document.getElementById('termSelect');
        const feeSelect  = document.getElementById('feeSelect');

        termSelect.innerHTML = '<option value="">Loading...</option>';
        feeSelect.innerHTML  = '<option value="">All Fees</option>';

        if (!sessionId) {
            termSelect.innerHTML = '<option value="">Select Term</option>';
            return;
        }

        fetch(`${routes.termsBySession}?session_id=${sessionId}`)
            .then(r => r.json())
            .then(terms => {
                termSelect.innerHTML = '<option value="">Select Term</option>';
                terms.forEach(t => {
                    termSelect.innerHTML += `<option value="${t.id}">${capitalize(t.name)} Term</option>`;
                });
                // Auto-submit to reload page with new session
                document.getElementById('filterForm').submit();
            });
    });

    // When term changes → reload fees via AJAX
    document.getElementById('termSelect').addEventListener('change', function () {
        const termId    = this.value;
        const feeSelect = document.getElementById('feeSelect');

        feeSelect.innerHTML = '<option value="">Loading...</option>';

        if (!termId) {
            feeSelect.innerHTML = '<option value="">All Fees</option>';
            document.getElementById('filterForm').submit();
            return;
        }

        fetch(`${routes.feesByTerm}?term_id=${termId}`)
            .then(r => r.json())
            .then(fees => {
                feeSelect.innerHTML = '<option value="">All Fees</option>';
                fees.forEach(f => {
                    feeSelect.innerHTML += `<option value="${f.id}">${f.name}</option>`;
                });
                // Auto-submit to reload page with new term
                document.getElementById('filterForm').submit();
            });
    });

    // When fee changes → submit form
    document.getElementById('feeSelect').addEventListener('change', function () {
        document.getElementById('filterForm').submit();
    });

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
</script>
@endsection
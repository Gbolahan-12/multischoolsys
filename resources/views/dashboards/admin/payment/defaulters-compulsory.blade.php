@extends('layouts.admin')
@section('title', 'Defaulters')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">Defaulters</h4>
            <small class="text-muted">Students who have not fully paid their compulsory fees</small>
        </div>
        <a href="{{ route('admin.payments.create') }}" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Record Payment
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3">
        {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── Filters ── --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form method="GET" id="filterForm">
                <div class="row g-2 align-items-end">

                    {{-- Session --}}
                    <div class="col-12 col-md-3">
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
                    <div class="col-12 col-md-3">
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

                    {{-- Search --}}
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold mb-1" style="font-size:12px;">Search</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search"
                                   value="{{ request('search') }}"
                                   class="form-control"
                                   placeholder="Name or admission no...">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                            @if(request()->hasAny(['search', 'term_id', 'session_id']))
                            <a href="{{ route('admin.payments.defaulters') }}"
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

    @if($summary)

    {{-- ── Summary Cards ── --}}
    <div class="row g-3 mb-4">
        @php
            $cards = [
                ['label' => 'Total Defaulters',  'value' => $summary['total_defaulters'],                            'icon' => 'bi-people',            'color' => '#dc3545'],
                ['label' => 'Never Paid',         'value' => $summary['no_payment'],                                 'icon' => 'bi-x-circle',          'color' => '#ef4444'],
                ['label' => 'Partial Payment',    'value' => $summary['partial'],                                    'icon' => 'bi-hourglass-split',   'color' => '#f59f00'],
                ['label' => 'Total Outstanding',  'value' => '₦' . number_format($summary['total_outstanding'], 2), 'icon' => 'bi-cash-stack',        'color' => '#6f42c1'],
            ];
        @endphp
        @foreach($cards as $card)
        <div class="col-6 col-lg-3">
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
                <i class="bi bi-exclamation-circle me-2 text-danger"></i>Defaulter List
            </h6>
            @if($defaulters->total() > 0)
            <span class="badge bg-danger rounded-pill">{{ $defaulters->total() }} students</span>
            @endif
        </div>

        <div class="card-body p-0">
            @if($defaulters->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">#</th>
                            <th class="py-3">Student</th>
                            <th class="py-3">Admission No.</th>
                            <th class="py-3">Class</th>
                            <th class="py-3 text-end">Amount Paid</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($defaulters as $i => $student)
                        @php
                            $paid = (float) ($student->amount_paid ?? 0);
                            $sc   = $paid > 0 ? 'warning' : 'danger';
                            $sl   = $paid > 0 ? 'Partial'  : 'Not Paid';
                        @endphp
                        <tr>
                            <td class="ps-4 text-muted">
                                {{ ($defaulters->currentPage() - 1) * $defaulters->perPage() + $i + 1 }}
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($student->photo)
                                    <img src="{{ asset('storage/' . $student->photo) }}"
                                         class="rounded-circle flex-shrink-0"
                                         style="width:32px;height:32px;object-fit:cover;">
                                    @else
                                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger fw-bold
                                                d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:32px;height:32px;font-size:12px;">
                                        {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                    </div>
                                    @endif
                                    <div class="fw-semibold">
                                        {{ $student->last_name }}, {{ $student->first_name }}
                                    </div>
                                </div>
                            </td>

                            <td class="text-muted">{{ $student->admission_number }}</td>

                            <td class="text-muted">
                                {{ $student->currentAssignment?->schoolClass?->name ?? '—' }}
                            </td>

                            <td class="text-end {{ $paid > 0 ? 'text-warning fw-semibold' : 'text-danger fw-semibold' }}">
                                ₦{{ number_format($paid, 2) }}
                            </td>

                            <td class="text-center">
                                <span class="badge bg-{{ $sc }}-subtle text-{{ $sc }} rounded-pill"
                                      style="font-size:11px;">
                                    {{ $sl }}
                                </span>
                            </td>

                            <td class="text-end pe-4">
                                <a href="{{ route('admin.payments.create', ['student_id' => $student->id]) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-cash me-1"></i> Pay
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($defaulters->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $defaulters->links('pagination::bootstrap-5') }}
            </div>
            @endif

            @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-check-circle d-block mb-2 text-success" style="font-size:2.5rem;opacity:.6;"></i>
                <p class="mb-0 fw-semibold">No defaulters found.</p>
                <small>All students have fully paid their compulsory fees for this term.</small>
            </div>
            @endif
        </div>
    </div>

    @else
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-filter d-block mb-3" style="font-size:2.5rem;opacity:.3;"></i>
            <p class="mb-0 fw-semibold">Select a session and term to view defaulters</p>
            <small>Use the filters above to get started.</small>
        </div>
    </div>
    @endif

</div>

<script>
const termsBySessionUrl = "{{ route('admin.payments.terms') }}";

document.getElementById('sessionSelect').addEventListener('change', function () {
    const termSelect = document.getElementById('termSelect');
    termSelect.innerHTML = '<option value="">Loading...</option>';

    if (!this.value) {
        termSelect.innerHTML = '<option value="">All Terms</option>';
        document.getElementById('filterForm').submit();
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
    document.getElementById('filterForm').submit();
});
</script>
@endsection
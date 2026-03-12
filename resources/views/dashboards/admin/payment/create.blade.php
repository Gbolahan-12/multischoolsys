@extends('layouts.admin')
@section('title', 'Record Payment')
@section('content')
<div class="container-fluid px-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Record Payment</h4>
            <small class="text-muted">
                @if($currentSession && $currentTerm)
                    {{ $currentSession->name }} &mdash; {{ ucfirst($currentTerm->name) }} Term
                @else
                    <span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>No active term set</span>
                @endif
            </small>
        </div>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-cash me-2 text-primary"></i>Payment Details
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.payments.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">

                            {{-- Student Dropdown --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">Student <span class="text-danger">*</span></label>
                                <select name="student_id" id="studentSelect"
                                        class="form-select @error('student_id') is-invalid @enderror" required>
                                    <option value="">-- Select Student --</option>
                                    @foreach($students as $s)
                                    <option value="{{ $s->id }}"
                                            {{ (old('student_id', $student?->id) == $s->id) ? 'selected' : '' }}>
                                        {{ $s->full_name }} — {{ $s->admission_number }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Fee Dropdown --}}
                            <div class="col-12">
                                <label class="form-label fw-medium">Fee <span class="text-danger">*</span></label>
                                <select name="fee_id" id="feeSelect"
                                        class="form-select @error('fee_id') is-invalid @enderror" required>
                                    <option value="">-- Select Student First --</option>
                                    @foreach($fees as $fee)
                                    <option value="{{ $fee->id }}"
                                            data-amount="{{ $fee->amount }}"
                                            data-paid="0"
                                            data-balance="{{ $fee->amount }}"
                                            {{ old('fee_id') == $fee->id ? 'selected' : '' }}>
                                        {{ $fee->feeType->name }}
                                        {{ $fee->schoolClass ? "({$fee->schoolClass->full_name})" : '(All Classes)' }}
                                        — ₦{{ number_format($fee->amount, 0) }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('fee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Fee Status Panel --}}
                            <div class="col-12 d-none" id="feeStatusPanel">
                                <div class="rounded-3 p-3 border bg-light">
                                    <div class="row g-2 text-center">
                                        <div class="col-4">
                                            <small class="text-muted d-block mb-1" style="font-size:11px;">Total Fee</small>
                                            <strong id="feeAmount">—</strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block mb-1" style="font-size:11px;">Already Paid</small>
                                            <strong class="text-success" id="feePaid">—</strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block mb-1" style="font-size:11px;">Balance</small>
                                            <strong class="text-danger" id="feeBalance">—</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Amount Paid (₦) <span class="text-danger">*</span></label>
                                <input type="number" name="amount_paid" id="amountPaid"
                                       class="form-control @error('amount_paid') is-invalid @enderror"
                                       min="1" step="1" value="{{ old('amount_paid') }}" required>
                                @error('amount_paid')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date"
                                       class="form-control @error('payment_date') is-invalid @enderror"
                                       value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                                @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method"
                                        class="form-select @error('payment_method') is-invalid @enderror" required>
                                    <option value="">-- Select --</option>
                                    <option value="cash"          {{ old('payment_method') === 'cash'          ? 'selected' : '' }}>Cash</option>
                                    <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="pos"        {{ old('payment_method') === 'pos'        ? 'selected' : '' }}>Pos</option>
                                    <option value="cheque"        {{ old('payment_method') === 'cheque'        ? 'selected' : '' }}>Cheque</option>
                                </select>
                                @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label class="form-label fw-medium">Reference</label>
                                <input type="text" name="reference" class="form-control"
                                       value="{{ old('reference') }}" placeholder="Receipt / transaction ref">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium">Note</label>
                                <input type="text" name="note" class="form-control"
                                       value="{{ old('note') }}" placeholder="Optional note">
                            </div>

                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle me-1"></i> Record Payment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Help --}}
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-info-circle me-2 text-primary"></i>How It Works
                    </h6>
                </div>
                <div class="card-body p-4 text-muted" style="font-size:13px;">
                    <div class="d-flex gap-2 mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 fw-bold" style="width:24px;height:24px;font-size:11px;">1</div>
                        <div>Select the student from the dropdown</div>
                    </div>
                    <div class="d-flex gap-2 mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 fw-bold" style="width:24px;height:24px;font-size:11px;">2</div>
                        <div>Select the fee — balance loads automatically</div>
                    </div>
                    <div class="d-flex gap-2 mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 fw-bold" style="width:24px;height:24px;font-size:11px;">3</div>
                        <div>Enter amount paid — partial payments are tracked</div>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 fw-bold" style="width:24px;height:24px;font-size:11px;">4</div>
                        <div>Status (Paid / Partial / Owing) is set automatically</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// When student changes, reload fees for that student
document.getElementById('studentSelect').addEventListener('change', function () {
    const studentId = this.value;
    const feeSelect = document.getElementById('feeSelect');
    const panel     = document.getElementById('feeStatusPanel');

    panel.classList.add('d-none');
    feeSelect.innerHTML = '<option value="">Loading fees...</option>';
    feeSelect.disabled  = true;

    if (!studentId) {
        feeSelect.innerHTML = '<option value="">-- Select Student First --</option>';
        feeSelect.disabled  = false;
        return;
    }

    fetch(`{{ route('admin.payments.student-fees') }}?student_id=${studentId}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(fees => {
        feeSelect.innerHTML = '<option value="">-- Select Fee --</option>';
        feeSelect.disabled  = false;

        if (!fees.length) {
            feeSelect.innerHTML = '<option value="" disabled>No fees for current term</option>';
            return;
        }

        fees.forEach(f => {
            const opt           = document.createElement('option');
            opt.value           = f.id;
            opt.dataset.amount  = f.amount;
            opt.dataset.paid    = f.amount_paid;
            opt.dataset.balance = f.balance;
            opt.textContent     = `${f.name} — ₦${Number(f.amount).toLocaleString()} (${f.status})`;
            feeSelect.appendChild(opt);
        });
    })
    .catch(() => {
        feeSelect.innerHTML = '<option value="">Error loading fees</option>';
        feeSelect.disabled  = false;
    });
});

// When fee changes, show status panel and prefill amount
document.getElementById('feeSelect').addEventListener('change', function () {
    const opt   = this.options[this.selectedIndex];
    const panel = document.getElementById('feeStatusPanel');

    if (!opt.value || !opt.dataset.amount) {
        panel.classList.add('d-none');
        return;
    }

    document.getElementById('feeAmount').textContent  = '₦' + Number(opt.dataset.amount).toLocaleString();
    document.getElementById('feePaid').textContent    = '₦' + Number(opt.dataset.paid).toLocaleString();
    document.getElementById('feeBalance').textContent = '₦' + Number(opt.dataset.balance).toLocaleString();

    document.getElementById('amountPaid').value = opt.dataset.balance > 0 ? Math.round(opt.dataset.balance) : '';
    panel.classList.remove('d-none');
});
</script>
@endpush

@endsection
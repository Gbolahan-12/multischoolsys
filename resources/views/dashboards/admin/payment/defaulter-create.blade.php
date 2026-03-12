@extends('layouts.admin')
@section('title', 'Record Defaulter Payment')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.payments.defaulter.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Record Defaulter Payment</h4>
            <small class="text-muted">Optional fees e.g. Uniform, Excursion, Books</small>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger rounded-3">
        @foreach($errors->all() as $error)
        <div><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</div>
        @endforeach
    </div>
    @endif

    {{-- Info banner --}}
    <div class="alert rounded-3 mb-4 d-flex align-items-start gap-2"
         style="background:#fff8e1;border:1px solid #ffe082;color:#6d4c00;">
        <i class="bi bi-info-circle-fill mt-1 flex-shrink-0" style="color:#f59f00;"></i>
        <div style="font-size:13px;">
            <strong>Defaulter / Optional Fee</strong> — This payment is for a fee that is
            not compulsory every term. For example, a student may buy a school uniform once
            and not need to pay again next term. This will <strong>not</strong> mark the
            student as owing if unpaid.
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold"
                        style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                        Payment Details
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.payments.defaulter.store') }}" method="POST">
                        @csrf

                        {{-- Student --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Student <span class="text-danger">*</span>
                            </label>
                            <select name="student_id" id="studentSelect"
                                    class="form-select @error('student_id') is-invalid @enderror" required>
                                <option value="">Select student...</option>
                                @foreach($students as $s)
                                <option value="{{ $s->id }}"
                                    {{ (old('student_id', $student?->id) == $s->id) ? 'selected' : '' }}>
                                    {{ $s->last_name }}, {{ $s->first_name }}
                                    ({{ $s->admission_number }})
                                </option>
                                @endforeach
                            </select>
                            @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Fee --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Defaulter Fee <span class="text-danger">*</span>
                            </label>
                            <select name="fee_id" id="feeSelect"
                                    class="form-select @error('fee_id') is-invalid @enderror" required>
                                <option value="">Select fee...</option>
                                @foreach($fees as $fee)
                                <option value="{{ $fee->id }}"
                                        data-amount="{{ $fee->amount }}"
                                    {{ old('fee_id') == $fee->id ? 'selected' : '' }}>
                                    {{ $fee->feeType->name }}
                                    — ₦{{ number_format($fee->amount, 2) }}
                                    {{ $fee->schoolClass ? '(' . $fee->schoolClass->name . ')' : '(All Classes)' }}
                                </option>
                                @endforeach
                            </select>
                            @error('fee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($fees->isEmpty())
                            <small class="text-warning">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                No defaulter fees found for the current term.
                                <a href="{{ route('admin.fees.index') }}">Create one</a>
                            </small>
                            @endif
                        </div>

                        {{-- Fee amount hint --}}
                        <div id="feeHint" class="mb-3 d-none">
                            <div class="rounded-3 p-3" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                                <div class="d-flex justify-content-between" style="font-size:13px;">
                                    <span class="text-muted">Fee Amount:</span>
                                    <span class="fw-bold text-success" id="feeAmountDisplay">—</span>
                                </div>
                            </div>
                        </div>

                        {{-- Amount Paid --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Amount Paid (₦) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="amount_paid" id="amountPaid"
                                   class="form-control @error('amount_paid') is-invalid @enderror"
                                   value="{{ old('amount_paid') }}"
                                   min="1" step="0.01" placeholder="0.00" required>
                            @error('amount_paid')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">

                            {{-- Payment Date --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" style="font-size:13px;">
                                    Payment Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="payment_date"
                                       class="form-control @error('payment_date') is-invalid @enderror"
                                       value="{{ old('payment_date', now()->toDateString()) }}" required>
                                @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Payment Method --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" style="font-size:13px;">
                                    Payment Method <span class="text-danger">*</span>
                                </label>
                                <select name="payment_method"
                                        class="form-select @error('payment_method') is-invalid @enderror" required>
                                    <option value="">Select method</option>
                                    @foreach(['cash' => 'Cash', 'transfer' => 'Bank Transfer', 'pos' => 'POS', 'cheque' => 'Cheque'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('payment_method') === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        {{-- Reference --}}
                        <div class="mb-3 mt-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Reference / Receipt No.
                            </label>
                            <input type="text" name="reference"
                                   class="form-control @error('reference') is-invalid @enderror"
                                   value="{{ old('reference') }}"
                                   placeholder="Optional transaction reference">
                            @error('reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Note --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="font-size:13px;">Note</label>
                            <textarea name="note" rows="2"
                                      class="form-control @error('note') is-invalid @enderror"
                                      placeholder="Optional note...">{{ old('note') }}</textarea>
                            @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 fw-semibold">
                                <i class="bi bi-save me-2"></i>Record Payment
                            </button>
                            <a href="{{ route('admin.payments.defaulter.index') }}"
                               class="btn btn-outline-secondary px-4">Cancel</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Show fee amount hint when fee is selected
document.getElementById('feeSelect').addEventListener('change', function () {
    const selected = this.options[this.selectedIndex];
    const amount   = selected.dataset.amount;
    const hint     = document.getElementById('feeHint');
    const display  = document.getElementById('feeAmountDisplay');

    if (amount) {
        display.textContent = '₦' + parseFloat(amount).toLocaleString('en-NG', {minimumFractionDigits: 2});
        hint.classList.remove('d-none');
        // Auto-fill amount paid
        document.getElementById('amountPaid').value = parseFloat(amount).toFixed(2);
    } else {
        hint.classList.add('d-none');
        document.getElementById('amountPaid').value = '';
    }
});
</script>
@endsection
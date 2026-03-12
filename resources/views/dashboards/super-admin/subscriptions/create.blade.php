@extends('layouts.superadmin')
@section('title', 'Record Subscription Payment')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('superadmin.subscriptions.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0">Record Subscription Payment</h4>
            <small class="text-muted">Record a school's subscription and set expiry</small>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger rounded-3">
        @foreach($errors->all() as $error)
        <div><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</div>
        @endforeach
    </div>
    @endif

    <div class="row g-4">

        {{-- ── Left: Form ── --}}
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold"
                        style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                        Payment Details
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('superadmin.subscriptions.store') }}" method="POST">
                        @csrf

                        {{-- School --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                School <span class="text-danger">*</span>
                            </label>
                            <select name="school_id" id="schoolSelect"
                                    class="form-select @error('school_id') is-invalid @enderror" required>
                                <option value="">Select school...</option>
                                @foreach($schools as $school)
                                <option value="{{ $school->id }}"
                                    {{ (old('school_id', $selectedSchool?->id) == $school->id) ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('school_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- School info panel (AJAX populated) --}}
                        <div id="schoolInfoPanel" class="mb-3 d-none">
                            <div class="rounded-3 p-3" style="background:#f8f9ff;border:1px solid #c5cae9;">
                                <div class="row g-2" style="font-size:13px;">
                                    <div class="col-6">
                                        <div class="text-muted" style="font-size:11px;">Current Status</div>
                                        <div id="infoStatus" class="fw-semibold"></div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted" style="font-size:11px;">Subscription Expires</div>
                                        <div id="infoExpires" class="fw-semibold"></div>
                                    </div>
                                </div>
                                <div id="infoWarning" class="mt-2 d-none"
                                     style="font-size:12px;color:#b45309;background:#fef3c7;padding:6px 10px;border-radius:6px;">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    <span id="infoWarningText"></span>
                                </div>
                                <div id="infoExtend" class="mt-2 d-none"
                                     style="font-size:12px;color:#0d6efd;background:#eff6ff;padding:6px 10px;border-radius:6px;">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Subscription still active — new payment will <strong>extend</strong> from current expiry date.
                                </div>
                            </div>
                        </div>

                        {{-- Duration --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Subscription Duration <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2">
                                @foreach([1 => '1 Month', 3 => '3 Months', 6 => '6 Months', 12 => '1 Year'] as $months => $label)
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="duration_months"
                                           id="duration_{{ $months }}" value="{{ $months }}"
                                           {{ old('duration_months') == $months ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary w-100" for="duration_{{ $months }}">
                                        {{ $label }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @error('duration_months')
                            <div class="text-danger mt-1" style="font-size:12px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Amount --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Amount Paid (₦) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="amount"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount') }}"
                                   min="1" step="0.01" placeholder="0.00" required>
                            @error('amount')
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
                                    @foreach(['cash'=>'Cash','transfer'=>'Bank Transfer','pos'=>'POS','cheque'=>'Cheque'] as $val => $label)
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
                        </div>

                        {{-- Note --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="font-size:13px;">Note</label>
                            <textarea name="note" rows="2" class="form-control"
                                      placeholder="Optional note...">{{ old('note') }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1 fw-semibold">
                                <i class="bi bi-save me-2"></i>Record Subscription
                            </button>
                            <a href="{{ route('superadmin.subscriptions.index') }}"
                               class="btn btn-outline-secondary px-4">Cancel</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- ── Right: Info Panel ── --}}
        <div class="col-12 col-lg-5">

            {{-- Duration guide --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold"
                        style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                        How Subscriptions Work
                    </h6>
                </div>
                <div class="card-body p-4" style="font-size:13px;">
                    <div class="d-flex align-items-start gap-2 mb-3">
                        <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                        <div>School is <strong>activated</strong> immediately when payment is recorded.</div>
                    </div>
                    <div class="d-flex align-items-start gap-2 mb-3">
                        <i class="bi bi-arrow-right-circle-fill text-primary mt-1 flex-shrink-0"></i>
                        <div>If school still has active subscription, new payment <strong>extends</strong> from the current expiry date.</div>
                    </div>
                    <div class="d-flex align-items-start gap-2 mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-warning mt-1 flex-shrink-0"></i>
                        <div>After expiry, school enters a <strong>{{ App\Services\SubscriptionService::GRACE_PERIOD_DAYS }}-day grace period</strong> with a warning.</div>
                    </div>
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-slash-circle-fill text-danger mt-1 flex-shrink-0"></i>
                        <div>After grace period, school is <strong>automatically suspended</strong> with reason "Subscription expired".</div>
                    </div>
                </div>
            </div>

            {{-- Recent subscriptions if school selected --}}
            @if($selectedSchool && $selectedSchool->subscriptions->count())
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold"
                        style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                        Recent Payments — {{ $selectedSchool->name }}
                    </h6>
                </div>
                <div class="card-body p-0">
                    @foreach($selectedSchool->subscriptions as $sub)
                    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom"
                         style="font-size:13px;">
                        <div>
                            <div class="fw-semibold">{{ $sub->duration_label }}</div>
                            <small class="text-muted">
                                {{ $sub->starts_at->format('d M Y') }} →
                                {{ $sub->expires_at->format('d M Y') }}
                            </small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-success">₦{{ number_format($sub->amount, 2) }}</div>
                            <small class="text-muted">{{ $sub->payment_date->format('d M Y') }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
const schoolInfoUrl = "{{ route('superadmin.subscriptions.school-info') }}";

document.getElementById('schoolSelect').addEventListener('change', function () {
    const schoolId = this.value;
    const panel    = document.getElementById('schoolInfoPanel');

    if (!schoolId) { panel.classList.add('d-none'); return; }

    fetch(`${schoolInfoUrl}?school_id=${schoolId}`)
        .then(r => r.json())
        .then(data => {
            if (!data) { panel.classList.add('d-none'); return; }

            document.getElementById('infoStatus').textContent  = data.status.charAt(0).toUpperCase() + data.status.slice(1);
            document.getElementById('infoExpires').textContent = data.subscription_expires_at ?? 'No subscription yet';

            const warning = document.getElementById('infoWarning');
            const extend  = document.getElementById('infoExtend');
            const warnTxt = document.getElementById('infoWarningText');

            warning.classList.add('d-none');
            extend.classList.add('d-none');

            if (data.is_expired) {
                warning.classList.remove('d-none');
                warnTxt.textContent = 'Subscription has expired. New payment starts from today.';
            } else {
                extend.classList.remove('d-none');
            }

            panel.classList.remove('d-none');
        });
});

// Trigger if school pre-selected
if (document.getElementById('schoolSelect').value) {
    document.getElementById('schoolSelect').dispatchEvent(new Event('change'));
}
</script>
@endsection
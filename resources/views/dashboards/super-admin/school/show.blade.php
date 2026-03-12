@extends('layouts.superadmin')
@section('title', $school->name)

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Back + Header --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('superadmin.schools.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left-short"></i>
        </a>
        <div class="flex-grow-1">
            <h4 class="fw-bold mb-0">{{ $school->name }}</h4>
            <small class="text-muted">School details &amp; management</small>
        </div>
        <div class="d-flex gap-2">
            @if($school->isPending())
            <form action="{{ route('superadmin.schools.activate', $school) }}" method="POST">
                @csrf
                <button class="btn btn-success btn-sm px-3"
                        onclick="return confirm('Activate this school?')">
                    <i class="feather-check me-1"></i> Activate School
                </button>
            </form>
            @endif

            @if($school->isActive())
            <button class="btn btn-danger btn-sm px-3"
                    data-bs-toggle="modal" data-bs-target="#banModal">
                <span><i class="bi bi-slash-circle me1"></i></span> Ban School
            </button>
            @endif

            @if($school->isBanned())
            <form action="{{ route('superadmin.schools.reactivate', $school) }}" method="POST">
                @csrf
                <button class="btn btn-warning btn-sm px-3"
                        onclick="return confirm('Reactivate this school?')">
                    <i class="feather-refresh-cw me-1"></i> Reactivate
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Status Banner --}}
    @if($school->isBanned())
    <div class="alert alert-danger rounded-3 d-flex align-items-start gap-2 mb-4">
        <i class="feather-slash mt-1 flex-shrink-0"></i>
        <div>
            <strong>This school is banned.</strong>
            @if($school->ban_reason) Reason: {{ $school->ban_reason }}. @endif
            Banned {{ $school->banned_at->diffForHumans() }}.
        </div>
    </div>
    @elseif($school->isPending())
    <div class="alert alert-warning rounded-3 d-flex align-items-start gap-2 mb-4">
        <i class="feather-clock mt-1 flex-shrink-0"></i>
        <div>
            <strong>Awaiting activation.</strong>
            This school registered {{ $school->created_at->diffForHumans() }} and has not yet been activated.
        </div>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold" style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                        School Information
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $info = [
                            'Email'   => $school->email   ?? '—',
                            'Phone'   => $school->phone   ?? '—',
                            'Address' => $school->address ?? '—',
                            'Motto'   => $school->motto   ?? '—',
                            'Status'  => $school->status_badge,
                            'Registered' => $school->created_at->format('d M Y'),
                        ];
                        if($school->activated_at)
                            $info['Activated'] = $school->activated_at->format('d M Y H:i');
                        if($school->banned_at)
                            $info['Banned'] = $school->banned_at->format('d M Y H:i');
                    @endphp

                    @foreach($info as $label => $value)
                    <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:13px;">
                        <span class="text-muted fw-semibold">{{ $label }}</span>
                        <span class="text-end">{!! $value !!}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">

            {{-- Quick stats --}}
            <div class="row g-3 mb-4">
                @php $stats = [
                    ['label' => 'Total Students', 'value' => $school->students_count, 'color' => '#0d6efd'],
                    ['label' => 'Total Staff',    'value' => $staffCount,             'color' => '#198754'],
                    ['label' => 'Admin Users',    'value' => $adminCount,             'color' => '#6f42c1'],
                ]; @endphp
                @foreach($stats as $s)
                <div class="col-4">
                    <div class="card border-0 shadow-sm rounded-3 text-center p-3">
                        <div class="fw-bold" style="font-size:1.8rem;color:{{ $s['color'] }};">
                            {{ number_format($s['value']) }}
                        </div>
                        <div class="text-muted" style="font-size:12px;">{{ $s['label'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Proprietor card --}}
            @php $proprietor = $school->users->first(); @endphp
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold" style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                        Proprietor
                    </h6>
                </div>
                <div class="card-body">
                    @if($proprietor)
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold"
                             style="width:48px;height:48px;font-size:18px;flex-shrink:0;">
                            {{ strtoupper(substr($proprietor->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $proprietor->name }}</div>
                            <div class="text-muted" style="font-size:13px;">{{ $proprietor->email }}</div>
                            <small class="text-muted">Joined {{ $proprietor->created_at->format('d M Y') }}</small>
                        </div>
                    </div>
                    @else
                    <p class="text-muted mb-0" style="font-size:13px;">No proprietor found.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Ban Modal --}}
@if($school->isActive())
<div class="modal fade" id="banModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('superadmin.schools.ban', $school) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">
                    <i class="feather-slash text-danger me-2"></i>Ban {{ $school->name }}
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="text-muted" style="font-size:14px;">
                    All users in this school will be locked out immediately.
                </p>
                <div>
                    <label class="form-label fw-semibold" style="font-size:13px;">
                        Reason <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="reason" class="form-control"
                           placeholder="e.g. Payment expired" required>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-danger">
                    <span><i class="bi bi-slash-circle me-1"></i></span> Ban School
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
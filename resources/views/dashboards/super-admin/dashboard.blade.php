@extends('layouts.superadmin')
@section('title', 'Super Admin Dashboard')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">Platform Overview</h4>
            <small class="text-muted">All schools across the system</small>
        </div>
        <a href="{{ route('superadmin.schools.index') }}" class="btn btn-primary btn-sm px-3">
            <i class="feather-list me-1"></i> Manage Schools
        </a>
    </div>

    <div class="row g-3 mb-4">
        @php
            $cards = [
                ['label' => 'Total Schools',   'value' => $stats['total_schools'],   'icon' => 'feather-home',        'color' => '#0d6efd'],
                ['label' => 'Active Schools',  'value' => $stats['active_schools'],  'icon' => 'feather-check-circle','color' => '#198754'],
                ['label' => 'Pending Payment', 'value' => $stats['pending_schools'], 'icon' => 'feather-clock',       'color' => '#ffc107'],
                ['label' => 'Banned Schools',  'value' => $stats['banned_schools'],  'icon' => 'feather-slash',       'color' => '#dc3545'],
                ['label' => 'Total Students',  'value' => $stats['total_students'],  'icon' => 'feather-users',       'color' => '#6f42c1'],
                ['label' => 'Total Staff',     'value' => $stats['total_staff'],     'icon' => 'feather-briefcase',   'color' => '#0dcaf0'],
            ];
        @endphp

        @foreach($cards as $card)
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-2 d-flex align-items-center justify-content-center"
                             style="width:36px;height:36px;background:{{ $card['color'] }}1a;">
                            <i class="{{ $card['icon'] }}" style="color:{{ $card['color'] }};font-size:16px;"></i>
                        </div>
                    </div>
                    <div class="fw-bold" style="font-size:1.6rem;line-height:1;">{{ number_format($card['value']) }}</div>
                    <div class="text-muted" style="font-size:12px;">{{ $card['label'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-4">

        {{-- Pending Schools (needs action) --}}
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-semibold" style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                        <i class="feather-clock me-2 text-warning"></i>Awaiting Activation
                    </h6>
                    @if($pendingSchools->count())
                    <span class="badge bg-warning text-dark rounded-pill">{{ $pendingSchools->count() }}</span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @forelse($pendingSchools as $school)
                    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                        <div>
                            <div class="fw-semibold" style="font-size:14px;">{{ $school->name }}</div>
                            <small class="text-muted">
                                {{ $school->email ?? 'No email' }} &middot;
                                Registered {{ $school->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.schools.show', $school) }}"
                               class="btn btn-sm btn-outline-secondary">View</a>
                            <form action="{{ route('superadmin.schools.activate', $school) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-success"
                                        onclick="return confirm('Activate {{ $school->name }}?')">
                                    Activate
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="feather-check-circle d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
                        <small>No schools awaiting activation</small>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recently Registered Schools --}}
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold" style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                        <i class="feather-home me-2 text-primary"></i>Recently Registered
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-2">School</th>
                                    <th class="py-2">Proprietor</th>
                                    <th class="py-2">Status</th>
                                    <th class="py-2">Registered</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSchools as $school)
                                <tr>
                                    <td class="ps-4">
                                        <a href="{{ route('superadmin.schools.show', $school) }}"
                                           class="fw-semibold text-dark text-decoration-none">
                                            {{ $school->name }}
                                        </a>
                                    </td>
                                    <td class="text-muted">
                                        {{ optional($school->users->first())->name ?? '—' }}
                                    </td>
                                    <td>{!! $school->status_badge !!}</td>
                                    <td class="text-muted">{{ $school->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
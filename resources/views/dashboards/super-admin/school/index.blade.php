@extends('layouts.superadmin')
@section('title', 'Manage Schools')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">Schools</h4>
            <small class="text-muted">{{ $schools->total() }} schools registered on the platform</small>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control form-control-sm" placeholder="Search school name...">
                </div>
                <div class="col-auto">
                    @foreach(['', 'active', 'pending', 'banned'] as $status)
                    <a href="{{ request()->fullUrlWithQuery(['status' => $status, 'page' => 1]) }}"
                       class="btn btn-sm me-1 {{ request('status') === $status ? 'btn-primary' : 'btn-outline-secondary' }}">
                        {{ $status === '' ? 'All' : ucfirst($status) }}
                    </a>
                    @endforeach
                </div>
                <div class="col-auto">
                    <button class="btn btn-sm btn-primary">Search</button>
                    <a href="{{ route('superadmin.schools.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Schools Table --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">School</th>
                            <th class="py-3">Contact</th>
                            <th class="py-3 text-center">Students</th>
                            <th class="py-3 text-center">Staff</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Registered</th>
                            <th class="py-3 pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schools as $school)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $school->name }}</div>
                                <small class="text-muted">
                                    Proprietor: {{ optional($school->users->first())->fullname ?? '—' }}
                                </small>
                            </td>
                            <td>
                                <div style="font-size:13px;">{{ $school->email ?? '—' }}</div>
                                <small class="text-muted">{{ $school->phone ?? '—' }}</small>
                            </td>
                            <td class="text-center fw-semibold">{{ number_format($school->students_count) }}</td>
                            <td class="text-center fw-semibold">{{ number_format($school->users_count) }}</td>
                            <td>{!! $school->status_badge !!}</td>
                            <td class="text-muted">{{ $school->created_at->format('d M Y') }}</td>
                            <td class="pe-4 text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('superadmin.schools.show', $school) }}"
                                       class=" text-secondary" title="View details">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if($school->isPending())
                                    <form action="{{ route('superadmin.schools.activate', $school) }}" method="POST">
                                        @csrf
                                        <button class="btn-outline-0 border-0 bg-transparent" title="Activate"
                                                onclick="return confirm('Activate {{ addslashes($school->name) }}?')">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                    @endif

                                    @if($school->isActive())
                                    <button class="btn-outline-0 border-0 bg-transparent text-danger" title="Ban school"
                                            data-bs-toggle="modal"
                                            data-bs-target="#banModal{{ $school->id }}">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>
                                    @endif

                                    @if($school->isBanned())
                                    <form action="{{ route('superadmin.schools.reactivate', $school) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-warning" title="Reactivate"
                                                onclick="return confirm('Reactivate {{ addslashes($school->name) }}?')">
                                            <i class="feather-refresh-cw"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Ban Modal for this school --}}
                        @if($school->isActive())
                        <div class="modal fade" id="banModal{{ $school->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="{{ route('superadmin.schools.ban', $school) }}" method="POST" class="modal-content">
                                    @csrf
                                    <div class="modal-header border-0">
                                        <h6 class="modal-title fw-bold">
                                            <i class="feather-slash text-danger me-2"></i>Ban School
                                        </h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body pt-0">
                                        <p class="text-muted" style="font-size:14px;">
                                            You are about to ban <strong>{{ $school->name }}</strong>.
                                            All users will be locked out immediately.
                                        </p>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" style="font-size:13px;">
                                                Reason <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="reason"
                                                   class="form-control"
                                                   placeholder="e.g. Payment expired, Terms violated..."
                                                   required>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                                            Cancel
                                        </button>
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="feather-slash me-1"></i> Ban School
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif

                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="feather-inbox d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
                                No schools found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($schools->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $schools->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
@extends('layouts.proprietor')

@section('title', 'Sessions & Terms')

@section('content')
<div class="container-fluid px-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">Sessions & Terms</h4>
            <small class="text-muted">Manage academic sessions and their terms</small>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createSessionModal">
            <i class="bi bi-plus-circle me-1"></i> New Session
        </button>
    </div>
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Active Session Banner --}}
    @if($currentSession)
    <div class="card border-0 rounded-3 mb-4 text-white"
         style="background: linear-gradient(135deg, #1d4ed8, #4f46e5);">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col">
                    <small class="text-white text-opacity-75 text-uppercase fw-semibold" style="font-size:11px;letter-spacing:.08em;">
                        <i class="bi bi-broadcast me-1"></i> Currently Active
                    </small>
                    <h5 class="fw-bold mb-1 mt-1">{{ $currentSession->name }}</h5>
                    <span class="text-white text-opacity-75" style="font-size:13px;">
                        {{ $currentSession->start_date->format('d M Y') }} — {{ $currentSession->end_date->format('d M Y') }}
                    </span>
                </div>
                <div class="col-auto">
                    @if($currentSession->currentTerm)
                    <div class="text-end">
                        <small class="text-white text-opacity-75 d-block" style="font-size:11px;">Current Term</small>
                        <span class="badge bg-white text-primary fw-semibold px-3 py-2 mt-1">
                            {{ ucfirst($currentSession->currentTerm->name) }} Term
                        </span>
                    </div>
                    @else
                    <span class="badge bg-warning text-dark px-3 py-2">No active term set</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Sessions List --}}
    @forelse($sessions as $session)
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                {{-- Active indicator --}}
                @if($session->is_current)
                    <span class="badge bg-success rounded-pill"><i class="bi bi-broadcast me-1"></i>Active</span>
                @else
                    <span class="badge bg-light text-muted rounded-pill border">Inactive</span>
                @endif

                <div>
                    <h6 class="fw-bold mb-0">{{ $session->name }}</h6>
                    <small class="text-muted">
                        {{ $session->start_date->format('d M Y') }} — {{ $session->end_date->format('d M Y') }}
                        &bull; {{ $session->terms_count }} {{ Str::plural('term', $session->terms_count) }}
                    </small>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                {{-- Set as current --}}
                @if(!$session->is_current)
                <form action="{{ route('proprietor.sessions.set-current', $session) }}" method="POST">
                    @csrf @method('PATCH')
                    <button class="btn btn-outline-success btn-sm"
                            onclick="return confirm('Set {{ $session->name }} as the active session?')">
                        <i class="bi bi-check2-circle me-1"></i> Set Active
                    </button>
                </form>
                @endif

                {{-- Edit session --}}
                <button class="btn btn-outline-secondary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#editSessionModal{{ $session->id }}">
                    <i class="bi bi-pencil me-1"></i> Edit
                </button>

                {{-- Add term --}}
                <button class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#addTermModal{{ $session->id }}">
                    <i class="bi bi-plus me-1"></i> Add Term
                </button>

                {{-- Delete session --}}
                @if(!$session->is_current)
                <form action="{{ route('proprietor.sessions.destroy', $session) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm"
                            onclick="return confirm('Delete session {{ $session->name }}? This will also delete its terms.')">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Terms --}}
        <div class="card-body p-0">
            @if($session->terms->isEmpty())
            <div class="text-center py-4 text-muted" style="font-size:13px;">
                <i class="bi bi-calendar-x d-block fs-3 opacity-25 mb-2"></i>
                No terms yet.
                <button class="btn btn-link btn-sm p-0 ms-1"
                        data-bs-toggle="modal"
                        data-bs-target="#addTermModal{{ $session->id }}">Add one</button>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-2" style="font-size:12px;">Term</th>
                            <th class="py-2 d-none d-sm-table-cell" style="font-size:12px;">Start Date</th>
                            <th class="py-2 d-none d-sm-table-cell" style="font-size:12px;">End Date</th>
                            <th class="py-2" style="font-size:12px;">Status</th>
                            <th class="py-2 pe-4 text-end" style="font-size:12px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($session->terms->sortBy('name') as $term)
                        <tr>
                            <td class="ps-4 fw-semibold" style="font-size:14px;">
                                {{ ucfirst($term->name) }} Term
                            </td>
                            <td class="d-none d-sm-table-cell text-muted" style="font-size:13px;">
                                {{ $term->start_date->format('d M Y') }}
                            </td>
                            <td class="d-none d-sm-table-cell text-muted" style="font-size:13px;">
                                {{ $term->end_date->format('d M Y') }}
                            </td>
                            <td>
                                @if($term->is_current)
                                    <span class="badge bg-success rounded-pill">Active</span>
                                @else
                                    <span class="badge bg-light text-muted border rounded-pill">Inactive</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex gap-2 justify-content-end flex-wrap">
                                    @if(!$term->is_current)
                                    <form action="{{ route('proprietor.terms.set-current', $term) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-outline-success btn-sm"
                                                onclick="return confirm('Set {{ ucfirst($term->name) }} term as active?')">
                                            <i class="bi bi-check2-circle"></i>
                                            <span class="d-none d-md-inline ms-1">Set Active</span>
                                        </button>
                                    </form>
                                    @endif

                                    <button class="btn btn-outline-secondary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editTermModal{{ $term->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    @if(!$term->is_current)
                                    <form action="{{ route('proprietor.terms.destroy', $term) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Delete this term?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- Edit Session Modal --}}
    <div class="modal fade" id="editSessionModal{{ $session->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('proprietor.sessions.update', $session) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header border-bottom">
                        <h6 class="modal-title fw-bold">Edit Session</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Session Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ $session->name }}"
                                   class="form-control" placeholder="e.g. 2024/2025" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-medium">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date"
                                       value="{{ $session->start_date->format('Y-m-d') }}"
                                       class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-medium">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date"
                                       value="{{ $session->end_date->format('Y-m-d') }}"
                                       class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Term Modal --}}
    <div class="modal fade" id="addTermModal{{ $session->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('proprietor.sessions.terms.store', $session) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h6 class="modal-title fw-bold">Add Term — {{ $session->name }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Term <span class="text-danger">*</span></label>
                            <select name="name" class="form-select" required>
                                <option value="">-- Select Term --</option>
                                @foreach(['first','second','third'] as $t)
                                    @unless($session->terms->pluck('name')->contains($t))
                                    <option value="{{ $t }}">{{ ucfirst($t) }} Term</option>
                                    @endunless
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-medium">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-medium">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Add Term</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Term Modals --}}
    @foreach($session->terms as $term)
    <div class="modal fade" id="editTermModal{{ $term->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('proprietor.terms.update', $term) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header border-bottom">
                        <h6 class="modal-title fw-bold">Edit Term — {{ $session->name }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Term <span class="text-danger">*</span></label>
                            <select name="name" class="form-select" required>
                                @foreach(['first','second','third'] as $t)
                                <option value="{{ $t }}" {{ $term->name === $t ? 'selected' : '' }}>
                                    {{ ucfirst($t) }} Term
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-medium">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date"
                                       value="{{ $term->start_date->format('Y-m-d') }}"
                                       class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-medium">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date"
                                       value="{{ $term->end_date->format('Y-m-d') }}"
                                       class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    @empty
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-calendar3 fs-1 d-block opacity-25 mb-3"></i>
            <p class="mb-2">No sessions created yet.</p>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createSessionModal">
                <i class="bi bi-plus-circle me-1"></i> Create First Session
            </button>
        </div>
    </div>
    @endforelse

</div>

{{-- Create Session Modal --}}
<div class="modal fade" id="createSessionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('proprietor.sessions.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold">Create New Session</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Session Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="e.g. 2025/2026" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Format: YYYY/YYYY (e.g. 2025/2026)</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-medium">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                   value="{{ old('start_date') }}" required>
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-medium">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date') }}" required>
                            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Create Session</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
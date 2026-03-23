@extends('layouts.admin')
@section('title', 'Fees')
@section('content')
    <div class="container-fluid px-4">

        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-0">Fee Management</h4>
                <small class="text-muted">
                    @if($currentSession && $currentTerm)
                        {{ $currentSession->name }} &mdash; {{ ucfirst($currentTerm->name) }} Term
                    @else
                        <span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>No active session/term</span>
                    @endif
                </small>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#feeTypesModal">
                    <i class="bi bi-tags me-1"></i> Fee Types
                </button>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createFeeModal">
                    <i class="bi bi-plus-circle me-1"></i> New Fee
                </button>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <small class="text-muted d-block mb-1">Total Fees</small>
                        <h4 class="fw-bold mb-0 text-primary">{{ $totalFees }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <small class="text-muted d-block mb-1">Collected</small>
                        <h4 class="fw-bold mb-0 text-success">₦{{ number_format($totalCollected, 0) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <small class="text-muted d-block mb-1">Outstanding</small>
                        <h4 class="fw-bold mb-0 text-danger">₦{{ number_format($totalOutstanding, 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Fees grouped by type --}}
        @forelse($fees as $typeName => $typeFees)
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0">{{ $typeName }}</h6>
                    <span class="badge bg-primary rounded-pill">{{ $typeFees->count() }}
                        {{ Str::plural('fee', $typeFees->count()) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-2" style="font-size:12px;">Class</th>
                                    <th class="py-2" style="font-size:12px;">Amount</th>
                                    <th class="py-2 d-none d-md-table-cell" style="font-size:12px;">Term</th>
                                    <th class="py-2 d-none d-lg-table-cell" style="font-size:12px;">Collected</th>
                                    <th class="py-2 d-none d-lg-table-cell" style="font-size:12px;">Outstanding</th>
                                    <th class="py-2 pe-4 text-end" style="font-size:12px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($typeFees as $fee)
                                    <tr>
                                        <td class="ps-4" style="font-size:14px;">
                                            @if($fee->schoolClass)
                                                <span class="fw-semibold">{{ $fee->schoolClass->full_name }}</span>
                                            @else
                                                <span class="badge bg-info text-dark">All Classes</span>
                                            @endif
                                            @if($fee->description)
                                                <small class="text-muted d-block">{{ $fee->description }}</small>
                                            @endif
                                        </td>
                                        <td class="fw-semibold text-primary">₦{{ number_format($fee->amount, 0) }}</td>
                                        <td class="d-none d-md-table-cell text-muted" style="font-size:13px;">
                                            {{ ucfirst($fee->term?->name) }} Term
                                        </td>
                                        <td class="d-none d-lg-table-cell text-success" style="font-size:13px;">
                                            ₦{{ number_format($fee->total_collected, 0) }}
                                        </td>
                                        <td class="d-none d-lg-table-cell text-danger" style="font-size:13px;">
                                            ₦{{ number_format($fee->total_outstanding, 0) }}
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="d-flex gap-1 justify-content-end">
                                                <button class="btn btn-outline-secondary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editFeeModal{{ $fee->id }}">
                                                    <i data-feather="edit-3"></i>
                                                </button>
                                                <form action="{{ route('admin.fees.destroy', $fee) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Delete this fee?')">
                                                        <i data-feather="trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-cash-stack fs-1 d-block opacity-25 mb-3"></i>
                    <p class="mb-2">No fees created yet for this term.</p>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createFeeModal">
                        <i class="bi bi-plus-circle me-1"></i> Create First Fee
                    </button>
                </div>
            </div>
        @endforelse

    </div>

    {{-- ══════════════════════════════════════════════════════════
         ALL MODALS ARE OUTSIDE THE MAIN CONTENT DIV
         Edit Fee Type modals MUST be at the top level of <body>
         never nested inside another modal
    ══════════════════════════════════════════════════════════ --}}

    {{-- Create Fee Modal --}}
    <div class="modal fade" id="createFeeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin.fees.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h6 class="modal-title fw-bold">Create New Fee</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-medium">Fee Type <span class="text-danger">*</span></label>
                                <select name="fee_type_id" class="form-select" required>
                                    <option value="">-- Select Fee Type --</option>
                                    @foreach($feeTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @if($feeTypes->isEmpty())
                                    <div class="form-text text-warning">No fee types yet. Create one using the Fee Types button.</div>
                                @endif
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-medium">Session <span class="text-danger">*</span></label>
                                <select name="session_id" id="feeSessionSelect" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}" {{ $currentSession?->id == $session->id ? 'selected' : '' }}>
                                            {{ $session->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-medium">Term <span class="text-danger">*</span></label>
                                <select name="term_id" id="feeTermSelect" class="form-select" required>
                                    <option value="">-- Select Session First --</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" {{ $currentTerm?->id == $term->id ? 'selected' : '' }}>
                                            {{ ucfirst($term->name) }} Term
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Class</label>
                                <select name="class_id" class="form-select">
                                    <option value="">-- All Classes --</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->full_name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Leave as "All Classes" to apply to every class</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Amount (₦) <span class="text-danger">*</span></label>
                                <input type="number" name="amount" class="form-control" min="1" step="0.01" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Description</label>
                                <input type="text" name="description" class="form-control" placeholder="Optional note">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Create Fee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Fee Types Modal (list + create form only — NO edit modals inside) --}}
    <div class="modal fade" id="feeTypesModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold"><i class="bi bi-tags me-2"></i>Manage Fee Types</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Create fee type form --}}
                    <form action="{{ route('admin.fees.types.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm"
                                    placeholder="e.g. School Fees" required>
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label fw-medium">Fee Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select form-select-sm @error('type') is-invalid @enderror" required>
                                    <option value="">Select type</option>
                                    <option value="compulsory">Compulsory</option>
                                    <option value="optional">Optional</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-3">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-plus me-1"></i> Add Type
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Fee types list --}}
                    <div class="border rounded-3 overflow-hidden">
                        <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-2">Name</th>
                                    <th class="py-2 d-none d-sm-table-cell">Type</th>
                                    <th class="py-2 pe-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feeTypes as $type)
                                    <tr>
                                        <td class="ps-3 fw-semibold">{{ $type->name }}</td>
                                        <td class="d-none d-sm-table-cell">
                                            @if($type->type === 'compulsory')
                                                <span class="badge bg-primary-subtle text-primary rounded-pill" style="font-size:11px;">Compulsory</span>
                                            @elseif($type->type === 'optional')
                                                <span class="badge bg-warning-subtle text-warning rounded-pill" style="font-size:11px;">Optional</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="pe-3 text-end">
                                            <div class="d-flex gap-2 justify-content-end">
                                                {{-- Edit button closes feeTypesModal then opens editFeeTypeModal --}}
                                                <button class="btn btn-outline-secondary btn-sm"
                                                    onclick="openEditFeeType({{ $type->id }}, '{{ addslashes($type->name) }}', '{{ $type->type }}')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="{{ route('admin.fees.types.destroy', $type) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Delete {{ $type->name }}?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No fee types yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Edit Fee Type Modal (SINGLE modal, populated via JS — NOT nested) ── --}}
    <div class="modal fade" id="editFeeTypeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <form id="editFeeTypeForm" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header border-bottom">
                        <h6 class="modal-title fw-bold">Edit Fee Type</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editFeeTypeName" class="form-control" required>
                        </div>
                        <div>
                            <label class="form-label fw-medium">Type <span class="text-danger">*</span></label>
                            <select name="type" id="editFeeTypeType" class="form-select" required>
                                <option value="compulsory">Compulsory</option>
                                <option value="optional">Optional</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Fee Modals (one per fee — outside all other modals) --}}
    @foreach($fees->flatten() as $fee)
    <div class="modal fade" id="editFeeModal{{ $fee->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin.fees.update', $fee) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header border-bottom">
                        <h6 class="modal-title fw-bold">Edit Fee</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Amount (₦) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" value="{{ $fee->amount }}"
                                class="form-control" min="1" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    @push('scripts')
    <script>
        // Load terms when session changes in create fee modal
        document.getElementById('feeSessionSelect')?.addEventListener('change', function () {
            const sessionId = this.value;
            const termSelect = document.getElementById('feeTermSelect');
            termSelect.innerHTML = '<option value="">Loading...</option>';

            if (!sessionId) {
                termSelect.innerHTML = '<option value="">-- Select Session First --</option>';
                return;
            }

            fetch(`/admin/fees/terms-by-session/${sessionId}`)
                .then(r => r.json())
                .then(terms => {
                    termSelect.innerHTML = '<option value="">-- Select Term --</option>';
                    terms.forEach(t => {
                        termSelect.innerHTML += `<option value="${t.id}">${t.name.charAt(0).toUpperCase() + t.name.slice(1)} Term</option>`;
                    });
                });
        });

        // Open edit fee type modal:
        // 1. Close the feeTypesModal first
        // 2. Populate the single edit modal with the fee type data
        // 3. Open the edit modal
        function openEditFeeType(id, name, type) {
            // Build the update route dynamically
            const baseUrl = "{{ url('admin/fees/types') }}";
            document.getElementById('editFeeTypeForm').action = `${baseUrl}/${id}`;
            document.getElementById('editFeeTypeName').value  = name;
            document.getElementById('editFeeTypeType').value  = type;

            // Close parent modal first, then open edit modal after it's hidden
            const feeTypesModal = bootstrap.Modal.getInstance(document.getElementById('feeTypesModal'));
            const editModal     = new bootstrap.Modal(document.getElementById('editFeeTypeModal'));

            if (feeTypesModal) {
                document.getElementById('feeTypesModal').addEventListener('hidden.bs.modal', function handler() {
                    editModal.show();
                    // Remove listener so it doesn't fire again next time
                    document.getElementById('feeTypesModal').removeEventListener('hidden.bs.modal', handler);
                });
                feeTypesModal.hide();
            } else {
                editModal.show();
            }
        }
    </script>
    @endpush

@endsection
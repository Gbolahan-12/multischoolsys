@extends('layouts.admin')

@section('title', 'Subjects')

@section('content')
<div class="container-fluid px-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Subjects</h4>
            <small class="text-muted">Manage all subjects for your school</small>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createSubjectModal">
            <i class="bi bi-plus-circle me-1"></i> New Subject
        </button>
    </div>

    {{-- Alerts --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Subjects Table --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3" style="font-size:12px;">#</th>
                            <th class="py-3" style="font-size:12px;">Subject Name</th>
                            <th class="py-3 d-none d-sm-table-cell" style="font-size:12px;">Code</th>
                            <th class="py-3" style="font-size:12px;">Assigned to Classes</th>
                            <th class="py-3 pe-4 text-end" style="font-size:12px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $i => $subject)
                        <tr>
                            <td class="ps-4 text-muted" style="font-size:13px;">{{ $i + 1 }}</td>
                            <td>
                                <div class="fw-semibold" style="font-size:14px;">{{ $subject->name }}</div>
                                <small class="text-muted d-sm-none">{{ $subject->code ?? 'No code' }}</small>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                @if($subject->code)
                                    <span class="badge bg-light text-dark border font-monospace">{{ $subject->code }}</span>
                                @else
                                    <span class="text-muted" style="font-size:13px;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">
                                    {{ $subject->class_assignments_count }} {{ Str::plural('class', $subject->class_assignments_count) }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button class="btn btn-outline-secondary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editSubjectModal{{ $subject->id }}"
                                            title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Delete {{ $subject->name }}? This cannot be undone.')"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Edit Subject Modal --}}
                        <div class="modal fade" id="editSubjectModal{{ $subject->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('admin.subjects.update', $subject) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header border-bottom">
                                            <h6 class="modal-title fw-bold">Edit Subject</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-medium">Subject Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" value="{{ $subject->name }}"
                                                       class="form-control" required>
                                            </div>
                                            <div>
                                                <label class="form-label fw-medium">Code</label>
                                                <input type="text" name="code" value="{{ $subject->code }}"
                                                       class="form-control" placeholder="e.g. MTH">
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

                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-book fs-1 d-block opacity-25 mb-3"></i>
                                No subjects yet.
                                <button class="btn btn-link btn-sm p-0 ms-1"
                                        data-bs-toggle="modal" data-bs-target="#createSubjectModal">
                                    Add the first one.
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Create Subject Modal --}}
<div class="modal fade" id="createSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.subjects.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold">New Subject</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. Mathematics" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label fw-medium">Code</label>
                        <input type="text" name="code" value="{{ old('code') }}"
                               class="form-control" placeholder="e.g. MTH">
                        <div class="form-text">Optional short code for the subject</div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Create Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
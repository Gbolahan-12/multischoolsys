@extends('layouts.admin')

@section('title', 'Class Settings')

@section('content')
<div class="container-fluid px-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Class Settings</h4>
            <small class="text-muted">Manage levels and sections for your school</small>
        </div>
        <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Classes
        </a>
    </div>

    <div class="row g-4">

        {{-- Levels --}}
        {{-- <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-0">Class Levels</h6>
                        <small class="text-muted">e.g. Junior Secondary, Senior Secondary</small>
                    </div>
                    <span class="badge bg-primary rounded-pill">{{ $levels->count() }}</span>
                </div>
                <div class="card-body border-bottom pb-4">
                    <form action="{{ route('admin.classes.settings.levels.store') }}" method="POST">
                        @csrf
                        <div class="d-flex gap-2">
                            <input type="text" name="name"
                                   class="form-control form-control-sm @error('name') is-invalid @enderror"
                                   placeholder="e.g. Junior Secondary" required>
                            <button type="submit" class="btn btn-primary btn-sm text-nowrap">
                                <i class="bi bi-plus me-1"></i> Add
                            </button>
                        </div>
                        @error('name')<div class="invalid-feedback d-block mt-1">{{ $message }}</div>@enderror
                    </form>
                </div>
                <div class="card-body p-0">
                    @forelse($levels as $level)
                    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                        <div>
                            <div class="fw-semibold" style="font-size:14px;">{{ $level->name }}</div>
                            <small class="text-muted">{{ $level->classes_count }} {{ Str::plural('class', $level->classes_count) }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editLevelModal{{ $level->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.classes.settings.levels.destroy', $level) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Delete level {{ $level->name }}?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="modal fade" id="editLevelModal{{ $level->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content border-0 shadow">
                                <form action="{{ route('admin.classes.settings.levels.update', $level) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-header border-bottom">
                                        <h6 class="modal-title fw-bold">Edit Level</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <label class="form-label fw-medium">Level Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" value="{{ $level->name }}"
                                               class="form-control" required>
                                    </div>
                                    <div class="modal-footer border-top">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @empty
                    <div class="text-center text-muted py-4" style="font-size:13px;">
                        <i class="bi bi-layers d-block fs-3 opacity-25 mb-2"></i>
                        No levels yet. Add one above.
                    </div>
                    @endforelse
                </div>
            </div>
        </div> --}}

        {{-- Sections --}}
        <div class="col-12 col-lg-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-0">Class Sections</h6>
                        <small class="text-muted">e.g. A, B, Gold, Silver</small>
                    </div>
                    <span class="badge bg-success rounded-pill">{{ $sections->count() }}</span>
                </div>

                {{-- Add Section Form --}}
                <div class="card-body border-bottom pb-4">
                    <form action="{{ route('admin.classes.settings.sections.store') }}" method="POST">
                        @csrf
                        <div class="d-flex gap-2">
                            <input type="text" name="name"
                                   class="form-control form-control-sm @error('name') is-invalid @enderror"
                                   placeholder="e.g. A" required>
                            <button type="submit" class="btn btn-success btn-sm text-nowrap">
                                <i class="bi bi-plus me-1"></i> Add
                            </button>
                        </div>
                        @error('name')<div class="invalid-feedback d-block mt-1">{{ $message }}</div>@enderror
                    </form>
                </div>

                {{-- Sections List --}}
                <div class="card-body p-0">
                    @forelse($sections as $section)
                    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                        <div>
                            <div class="fw-semibold" style="font-size:14px;">{{ $section->name }}</div>
                            <small class="text-muted">{{ $section->classes_count }} {{ Str::plural('class', $section->classes_count) }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editSectionModal{{ $section->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.classes.settings.sections.destroy', $section) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Delete section {{ $section->name }}?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Edit Section Modal --}}
                    <div class="modal fade" id="editSectionModal{{ $section->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content border-0 shadow">
                                <form action="{{ route('admin.classes.settings.sections.update', $section) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-header border-bottom">
                                        <h6 class="modal-title fw-bold">Edit Section</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <label class="form-label fw-medium">Section Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" value="{{ $section->name }}"
                                               class="form-control" required>
                                    </div>
                                    <div class="modal-footer border-top">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    @empty
                    <div class="text-center text-muted py-4" style="font-size:13px;">
                        <i class="bi bi-grid d-block fs-3 opacity-25 mb-2"></i>
                        No sections yet. Add one above.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
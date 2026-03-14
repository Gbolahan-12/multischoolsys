@extends('layouts.admin')

@section('title', 'Classes')

@section('content')
    <div class="container-fluid px-4">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-0">Classes</h4>
                <small class="text-muted">
                    @if($currentSession)
                        Session: <strong>{{ $currentSession->name }}</strong>
                    @else
                        <span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>No active session</span>
                    @endif
                </small>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.classes.settings') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-gear me-1"></i> Levels & Sections
                </a>
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#subjectsModal">
                    <i class="bi bi-book me-1"></i> Manage Subjects
                </button>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createClassModal">
                    <i class="bi bi-plus-circle me-1"></i> New Class
                </button>
            </div>
        </div>
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($levels->isEmpty() || $sections->isEmpty())
            <div class="alert alert-info d-flex align-items-center gap-2 rounded-3 mb-4">
                <i class="bi bi-info-circle-fill fs-5"></i>
                <div>
                    @if($levels->isEmpty()) <strong>No levels yet.</strong> @endif
                    @if($sections->isEmpty()) <strong>No sections yet.</strong> @endif
                    <a href="{{ route('admin.classes.settings') }}" class="alert-link ms-1">
                        Set them up first &rarr;
                    </a>
                </div>
            </div>
        @endif

        {{-- Classes grouped by level --}}
        @forelse($classes as $level => $levelClasses)
            <div class="mb-4">
                <h6 class="text-uppercase text-muted fw-semibold mb-3" style="font-size:11px;letter-spacing:.08em;">
                    <i class="bi bi-layers me-1"></i>{{ $level }}
                </h6>
                <div class="row g-3">
                    @foreach($levelClasses as $class)
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-body p-4">

                                    {{-- Class Header --}}
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div>
                                            <h5 class="fw-bold mb-1">{{ $class->full_name }}</h5>
                                            <small class="text-muted">
                                                <i class="bi bi-person me-1"></i>
                                                {{ $class->formTeacher?->fullname ?? 'No form teacher' }}
                                            </small>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">
                                             <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                <li>
                                                    <button class="dropdown-item" data-bs-toggle="modal"
                                                        data-bs-target="#editClassModal{{ $class->id }}">
                                                        <i class="bi bi-pencil me-2 text-primary"></i> Edit
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item" data-bs-toggle="modal"
                                                        data-bs-target="#assignSubjectModal{{ $class->id }}">
                                                        <i class="bi bi-plus-circle me-2 text-success"></i> Assign Subject
                                                    </button>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.classes.destroy', $class) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <button class="dropdown-item text-danger"
                                                            onclick="return confirm('Delete {{ $class->full_name }}?')">
                                                            <i class="bi bi-trash me-2"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    {{-- Stats --}}
                                    <div class="d-flex gap-3 mb-3">
                                        <div class="text-center">
                                            <div class="fw-bold text-primary">{{ $class->students_count }}</div>
                                            <small class="text-muted" style="font-size:11px;">Students</small>
                                        </div>
                                        <div class="text-center">
                                            <div class="fw-bold text-success">{{ $class->subjectAssignments->count() }}</div>
                                            <small class="text-muted" style="font-size:11px;">Subjects</small>
                                        </div>
                                    </div>

                                    {{-- Subjects --}}
                                    @if($class->subjectAssignments->isNotEmpty())
                                        <div class="border-top pt-3">
                                            <small class="text-muted d-block mb-2 fw-semibold" style="font-size:11px;">SUBJECTS</small>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($class->subjectAssignments as $assignment)
                                                    <span class="badge bg-light text-dark border d-flex align-items-center gap-1"
                                                        style="font-size:11px;">
                                                        {{ $assignment->subject->name }}
                                                        <form action="{{ route('admin.classes.subjects.remove', $assignment) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-link p-0 text-danger lh-1"
                                                                style="font-size:10px;"
                                                                onclick="return confirm('Remove {{ $assignment->subject->name }}?')">&times;</button>
                                                        </form>
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="border-top pt-3">
                                            <small class="text-muted" style="font-size:12px;">
                                                <i class="bi bi-info-circle me-1"></i>No subjects assigned.
                                            </small>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>

                        {{-- Edit Class Modal --}}
                        <div class="modal fade" id="editClassModal{{ $class->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('admin.classes.update', $class) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header border-bottom">
                                            <h6 class="modal-title fw-bold">Edit — {{ $class->full_name }}</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label fw-medium">Class Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="name" value="{{ $class->name }}" class="form-control"
                                                        placeholder="e.g. JSS 1" required>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-medium">Level</label>
                                                    <select name="level_id" class="form-select">
                                                        <option value="">-- None --</option>
                                                        @foreach($levels as $level)
                                                            <option value="{{ $level->id }}" {{ $class->level_id == $level->id ? 'selected' : '' }}>
                                                                {{ $level->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-medium">Section</label>
                                                    <select name="section_id" class="form-select">
                                                        <option value="">-- None --</option>
                                                        @foreach($sections as $section)
                                                            <option value="{{ $section->id }}" {{ $class->section_id == $section->id ? 'selected' : '' }}>
                                                                {{ $section->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-medium">Form Teacher</label>
                                                    <select name="form_teacher_id" class="form-select">
                                                        <option value="">-- None --</option>
                                                        @foreach($staff as $s)
                                                            <option value="{{ $s->id }}" {{ $class->form_teacher_id == $s->id ? 'selected' : '' }}>
                                                                {{ $s->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top">
                                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Assign Subject Modal --}}
                        <div class="modal fade" id="assignSubjectModal{{ $class->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('admin.classes.subjects.assign', $class) }}" method="POST">
                                        @csrf
                                        <div class="modal-header border-bottom">
                                            <h6 class="modal-title fw-bold">Assign Subject — {{ $class->full_name }}</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            @php
                                                $assignedIds = $class->subjectAssignments->pluck('subject_id')->toArray();
                                                $availableSubjects = \App\Models\Subject::whereNotIn('id', $assignedIds)->orderBy('name')->get();
                                            @endphp
                                            @if($availableSubjects->isEmpty())
                                                <p class="text-muted text-center py-2 mb-0">All subjects are already assigned.</p>
                                            @else
                                                <div class="mb-3">
                                                    <label class="form-label fw-medium">Subject <span
                                                            class="text-danger">*</span></label>
                                                    <select name="subject_id" class="form-select" required>
                                                        <option value="">-- Select Subject --</option>
                                                        @foreach($availableSubjects as $subject)
                                                            <option value="{{ $subject->id }}">
                                                                {{ $subject->name }}{{ $subject->code ? " ({$subject->code})" : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="form-label fw-medium">Teacher</label>
                                                    <select name="teacher_id" class="form-select">
                                                        <option value="">-- None --</option>
                                                        @foreach($staff as $s)
                                                            <option value="{{ $s->id }}">{{ $s->fullname }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer border-top">
                                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                                data-bs-dismiss="modal">Cancel</button>
                                            @if($availableSubjects->isNotEmpty())
                                                <button type="submit" class="btn btn-primary btn-sm">Assign</button>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @endforeach
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-building fs-1 d-block opacity-25 mb-3"></i>
                    <p class="mb-2">No classes yet.</p>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createClassModal">
                        <i class="bi bi-plus-circle me-1"></i> Create First Class
                    </button>
                </div>
            </div>
        @endforelse

    </div>

    {{-- Create Class Modal --}}
    <div class="modal fade" id="createClassModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin.classes.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h6 class="modal-title fw-bold">Create New Class</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-medium">Class Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror" placeholder="e.g. JSS 1"
                                    required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-medium">Level</label>
                                <select name="level_id" class="form-select">
                                    <option value="">-- None --</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>
                                            {{ $level->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($levels->isEmpty())
                                    <div class="form-text text-warning">
                                        <a href="{{ route('admin.classes.settings') }}">Add levels first</a>
                                    </div>
                                @endif
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-medium">Section</label>
                                <select name="section_id" class="form-select">
                                    <option value="">-- None --</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($sections->isEmpty())
                                    <div class="form-text text-warning">
                                        <a href="{{ route('admin.classes.settings') }}">Add sections first</a>
                                    </div>
                                @endif
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Form Teacher</label>
                                <select name="form_teacher_id" class="form-select">
                                    <option value="">-- None --</option>
                                    @foreach($staff as $s)
                                        <option value="{{ $s->id }}" {{ old('form_teacher_id') == $s->id ? 'selected' : '' }}>
                                            {{ $s->fullname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Create Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Subjects Modal (same as before) --}}
    <div class="modal fade" id="subjectsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold"><i class="bi bi-book me-2"></i>Manage Subjects</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('admin.subjects.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-sm-5">
                                <label class="form-label fw-medium">Subject Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm"
                                    placeholder="e.g. Mathematics" required>
                            </div>
                            <div class="col-12 col-sm-3">
                                <label class="form-label fw-medium">Code</label>
                                <input type="text" name="code" class="form-control form-control-sm" placeholder="e.g. MTH">
                            </div>
                            <div class="col-12 col-sm-4">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-plus me-1"></i> Add Subject
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="border rounded-3 overflow-hidden">
                        <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-2">Subject</th>
                                    <th class="py-2">Code</th>
                                    <th class="py-2">Classes</th>
                                    <th class="py-2 pe-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\Subject::withCount('classAssignments')->orderBy('name')->get() as $subject)
                                    <tr>
                                        <td class="ps-3 fw-semibold">{{ $subject->name }}</td>
                                        <td class="text-muted">{{ $subject->code ?? '—' }}</td>
                                        <td><span
                                                class="badge bg-primary bg-opacity-10 text-primary rounded-pill">{{ $subject->class_assignments_count }}</span>
                                        </td>
                                        <td class="pe-3 text-end">
                                            <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST"
                                                class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Delete {{ $subject->name }}?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No subjects yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
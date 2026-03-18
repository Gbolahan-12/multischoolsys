@extends('layouts.admin')
@section('title', 'Students')
@section('content')
    <div class="container-fluid px-4">

        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-0">Students</h4>
                <small class="text-muted">
                    {{ $students->total() }} {{ Str::plural('student', $students->total()) }}
                    @if($currentSession) &bull; <strong>{{ $currentSession->name }}</strong> @endif
                    @if($currentTerm) &bull; {{ ucfirst($currentTerm->name) }} Term @endif
                </small>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.students.import.form') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-file-earmark-arrow-up me-1"></i> Import Excel
                </a>
                <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-person-plus me-1"></i> Add Student
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.students.index') }}">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-sm-4">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control form-control-sm" placeholder="Search name or admission no...">
                        </div>
                        <div class="col-12 col-sm-3">
                            <select name="class_id" class="form-select form-select-sm">
                                <option value="">All Classes</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-2">
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                <i class="bi bi-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-x"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3" style="font-size:12px;">Student</th>
                                <th class="py-3 d-none d-md-table-cell" style="font-size:12px;">Admission No.</th>
                                <th class="py-3 d-none d-sm-table-cell" style="font-size:12px;">Class</th>
                                <th class="py-3 d-none d-lg-table-cell" style="font-size:12px;">Guardian</th>
                                <th class="py-3" style="font-size:12px;">Status</th>
                                <th class="py-3 pe-4 text-end" style="font-size:12px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                    @if($student->photo)
                                        <img src="{{ asset('storage/' . $student->photo) }}"
                                            class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                                    @else
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold
                                                    d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width:36px;height:36px;font-size:14px;">
                                            {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                        </div>
                                    @endif
                                            <div>
                                                <div class="fw-semibold" style="font-size:14px;">{{ $student->short_name }}
                                                </div>
                                                <small class="text-muted d-sm-none">{{ $student->admission_number }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <span class="font-monospace"
                                            style="font-size:13px;">{{ $student->admission_number }}</span>
                                    </td>
                                    <td class="d-none d-sm-table-cell text-muted" style="font-size:13px;">
                                        {{ $student->currentAssignment?->schoolClass?->full_name ?? '—' }}
                                    </td>
                                    <td class="d-none d-lg-table-cell" style="font-size:13px;">
                                        <div>{{ $student->guardian_name }}</div>
                                        <small class="text-muted">{{ $student->guardian_phone }}</small>
                                    </td>
                                    <td>
                                        @if($student->is_active)
                                            <span class="badge bg-success rounded-pill">Active</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="{{ route('admin.students.show', $student) }}"
                                                class="btn btn-outline-primary btn-sm" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.students.edit', $student) }}"
                                                class="btn btn-outline-secondary btn-sm" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.students.toggle-status', $student) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button
                                                    class="btn btn-sm {{ $student->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                    title="{{ $student->is_active ? 'Deactivate' : 'Activate' }}"
                                                    onclick="return confirm('{{ $student->is_active ? 'Deactivate' : 'Activate' }} {{ $student->short_name }}?')">
                                                    <i
                                                        class="bi {{ $student->is_active ? 'bi-person-dash' : 'bi-person-check' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-people fs-1 d-block opacity-25 mb-3"></i>
                                        No students found.
                                        <a href="{{ route('admin.students.create') }}">Add the first one.</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($students->hasPages())
                    <div class="px-4 py-3 border-top">
                        {{ $students->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
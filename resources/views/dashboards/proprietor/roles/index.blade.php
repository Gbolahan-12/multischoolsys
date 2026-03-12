@extends('layouts.proprietor')
@section('title', 'Role Management')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">Roles & Permissions</h4>
            <small class="text-muted">Manage what each role can access in your school</small>
        </div>
        <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            <i class="feather-plus me-1"></i> Create Role
        </button>
    </div>

    @if($errors->any())
    <div class="alert alert-danger rounded-3">{{ $errors->first() }}</div>
    @endif

    {{-- Permission legend --}}
    @php
        $permissionGroups = [
            'students' => ['view-students','create-students','edit-students','delete-students'],
            'results'  => ['view-results','upload-results','edit-results'],
            'fees'     => ['view-fees','create-fees','edit-fees','delete-fees'],
            'payments' => ['view-payments','record-payment'],
            'reports'  => ['view-reports','download-reports'],
            'settings' => ['manage-settings'],
            'users'    => ['view-users','manage-users'],
        ];
        $groupColors = [
            'students' => '#0d6efd',
            'results'  => '#198754',
            'fees'     => '#fd7e14',
            'payments' => '#6f42c1',
            'reports'  => '#0dcaf0',
            'settings' => '#6c757d',
            'users'    => '#dc3545',
        ];
    @endphp

    {{-- Role cards --}}
    <div class="row g-4">
        @forelse($roles as $role)
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-2 bg-primary bg-opacity-10 px-2 py-1">
                            <span class="fw-bold text-primary" style="font-size:13px;">{{ ucfirst($role->name) }}</span>
                        </div>
                        <small class="text-muted">{{ $role->permissions->count() }} permissions</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#editRole{{ $role->id }}">
                            <i class="feather-edit-2"></i> Edit
                        </button>
                        @if(!in_array($role->name, ['admin', 'staff']))
                        <form action="{{ route('proprietor.roles.destroy', $role) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Delete role {{ $role->name }}?')">
                                <i class="feather-trash-2"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                <div class="card-body p-3">
                    @foreach($permissionGroups as $group => $perms)
                    @php
                        $rolePermNames = $role->permissions->pluck('name')->toArray();
                        $hasAny = count(array_intersect($perms, $rolePermNames)) > 0;
                    @endphp
                    <div class="mb-2">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="fw-semibold" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:{{ $groupColors[$group] }};">
                                {{ ucfirst($group) }}
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($perms as $perm)
                            @php $has = in_array($perm, $rolePermNames); @endphp
                            <span class="badge rounded-pill {{ $has ? 'text-white' : 'text-muted bg-light border' }}"
                                  style="{{ $has ? 'background:'.$groupColors[$group].';' : '' }} font-size:11px;">
                                {{ str_replace('-', ' ', $perm) }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Edit Role Modal --}}
        <div class="modal fade" id="editRole{{ $role->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <form action="{{ route('proprietor.roles.update', $role) }}" method="POST" class="modal-content">
                    @csrf @method('PUT')
                    <div class="modal-header border-0">
                        <h6 class="modal-title fw-bold">Edit permissions for: <em>{{ ucfirst($role->name) }}</em></h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @foreach($permissionGroups as $group => $perms)
                        <div class="mb-3">
                            <p class="fw-semibold mb-2" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:{{ $groupColors[$group] }};">
                                {{ ucfirst($group) }}
                            </p>
                            <div class="row g-2">
                                @foreach($perms as $perm)
                                <div class="col-6 col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="permissions[]" value="{{ $perm }}"
                                               id="edit_{{ $role->id }}_{{ $perm }}"
                                               {{ in_array($perm, $role->permissions->pluck('name')->toArray()) ? 'checked' : '' }}>
                                        <label class="form-check-label" style="font-size:13px;"
                                               for="edit_{{ $role->id }}_{{ $perm }}">
                                            {{ str_replace('-', ' ', $perm) }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-primary">Save Permissions</button>
                    </div>
                </form>
            </div>
        </div>

        @empty
        <div class="col-12">
            <div class="text-center py-5 text-muted">
                <i class="feather-shield d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
                No roles created yet. Roles are created automatically when your school is activated.
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- Create Role Modal --}}
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('proprietor.roles.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold"><i class="feather-plus me-2"></i>Create New Role</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label fw-semibold" style="font-size:13px;">Role Name</label>
                    <input type="text" name="name" class="form-control"
                           placeholder="e.g. gateman, accountant, librarian" required>
                    <small class="text-muted">Use lowercase, no spaces (use hyphens if needed)</small>
                </div>

                <p class="fw-semibold mb-3" style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                    Assign Permissions
                </p>

                @foreach($permissionGroups as $group => $perms)
                <div class="mb-3">
                    <p class="fw-semibold mb-2" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:{{ $groupColors[$group] }};">
                        {{ ucfirst($group) }}
                    </p>
                    <div class="row g-2">
                        @foreach($perms as $perm)
                        <div class="col-6 col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="permissions[]" value="{{ $perm }}"
                                       id="new_{{ $perm }}">
                                <label class="form-check-label" style="font-size:13px;" for="new_{{ $perm }}">
                                    {{ str_replace('-', ' ', $perm) }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-primary">Create Role</button>
            </div>
        </form>
    </div>
</div>
@endsection
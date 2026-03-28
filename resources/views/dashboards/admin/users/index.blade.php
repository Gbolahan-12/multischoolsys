@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
    <div class="container-fluid px-4">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-0">Staff</h4>
                <small class="text-muted">Manage admin and staff accounts</small>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-person-plus-fill me-1"></i> Create Account
            </a>
        </div>

        {{-- Temp Password Alert --}}
        {{-- @if(session('temp_password'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-check-circle-fill fs-5 mt-1"></i>
                <div class="flex-grow-1">
                    <strong>Account created! Share these credentials — shown only once.</strong>
                    <div class="row g-2 mt-2">
                        <div class="col-12 col-sm-6">
                            <div class="bg-white border rounded p-2">
                                <small class="text-muted d-block">Email</small>
                                <span class="fw-semibold font-monospace">{{ session('temp_email') }}</span>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="bg-white border rounded p-2">
                                <small class="text-muted d-block">Temporary Password</small>
                                <span class="fw-semibold font-monospace fs-5 letter-spacing-2">{{ session('temp_password')
                                    }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif --}}

        {{-- Table Card --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">Name</th>
                                <th class="py-3 d-none d-md-table-cell">Email</th>
                                <th class="py-3">Role</th>
                                <th class="py-3">Status</th>
                                <th class="py-3 d-none d-lg-table-cell">Joined</th>
                                <th>Last Login</th>
                                <th class="py-3 pe-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            {{-- <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold"
                                                style="width:36px;height:36px;font-size:13px;">
                                                {{ strtoupper(substr($user->fullname, 0, 1)) }}
                                            </div> --}}
                                            <img src="{{ asset($user->profile?->avatar ?? 'profile-images/default-avatar.jpg') }}"
     class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                                            <div>
                                                <div class="fw-semibold text-dark" style="font-size:14px;">{{ $user->fullname }}
                                                </div>
                                                <small class="text-muted d-md-none">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell text-muted" style="font-size:14px;">{{ $user->email }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge rounded-pill {{ $user->role === 'admin' ? 'bg-purple' : 'bg-info' }} text-white"
                                            style="{{ $user->role === 'admin' ? 'background:#7c3aed!important' : '' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->isBanned())
                                            <span class="badge bg-danger rounded-pill">Banned</span>
                                        @elseif($user->is_active)
                                            <span class="badge bg-success rounded-pill">Active</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell text-muted" style="font-size:13px;">
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>
                                    <td>
                                        @if($user->last_login_at)
                                            {{ $user->last_login_at->diffForHumans() }}
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                                                {{-- <i class="bi bi-three-dots"></i> --}}
                                                <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>

                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.users.edit', $user) }}">
                                                        <i class="bi bi-pencil me-2 text-primary"></i> Edit
                                                    </a>
                                                </li>
                                                {{-- <li>
                                                    <form action="{{ route('proprietor.users.reset-password', $user) }}"
                                                        method="POST">
                                                        @csrf @method('PATCH')
                                                        <button class="dropdown-item"
                                                            onclick="return confirm('Reset password for {{ $user->fullname }}?')">
                                                            <i class="bi bi-key me-2 text-warning"></i> Reset Password
                                                        </button>
                                                    </form>
                                                </li> --}}
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.users.show', $user) }}">
                                                        <i class="bi bi-eye me-2 text-secondary"></i> View Profile
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                @if($user->isBanned())
                                                    <li>
                                                        <form action="{{ route('admin.users.unban', $user) }}" method="POST">
                                                            @csrf @method('PATCH')
                                                            <button class="dropdown-item text-success">
                                                                <i class="bi bi-unlock me-2"></i> Unban
                                                            </button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <form action="{{ route('admin.users.ban', $user) }}" method="POST">
                                                            @csrf @method('PATCH')
                                                            <button class="dropdown-item text-warning"
                                                                onclick="return confirm('Ban {{ $user->fullname }}?')">
                                                                <i class="bi bi-slash-circle me-2"></i> Ban
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                <li>
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <button class="dropdown-item text-danger"
                                                            onclick="return confirm('Delete {{ $user->fullname }} permanently?')">
                                                            <i class="bi bi-trash me-2"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                                        No users yet.
                                        <a href="{{ route('admin.users.create') }}">Create the first one.</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-4 py-3 border-top">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
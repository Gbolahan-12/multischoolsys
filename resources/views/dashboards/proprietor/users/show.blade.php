@extends('layouts.proprietor')

@section('title', 'User Profile')

@section('content')
<div class="container-fluid px-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">User Profile</h4>
            <small class="text-muted">Viewing details for <strong>{{ $user->fullname }}</strong></small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('proprietor.users.edit', $user) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('proprietor.users.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    {{-- Temp Password Alert (after password reset) --}}
    @if(session('temp_password'))
    <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
        <div class="d-flex align-items-start gap-2">
            <i class="bi bi-check-circle-fill fs-5 mt-1"></i>
            <div class="flex-grow-1">
                <strong>Password reset! Share these credentials — shown only once.</strong>
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
                            <span class="fw-semibold font-monospace fs-5">{{ session('temp_password') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">

        {{-- Left: Avatar + Quick Info --}}
        <div class="col-12 col-lg-4">

            {{-- Profile Card --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4 text-center">
                <div class="card-body p-4">
                    {{-- Avatar --}}
                    {{-- <div class="mx-auto rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold mb-3"
                         style="width:80px;height:80px;font-size:28px;">
                        {{ strtoupper(substr($user->fullname, 0, 1)) }}
                    </div> --}}
                    <img src="{{ asset($user->profile?->avatar ?? 'profile-images/default-avatar.jpg') }}"
                                                class="rounded-circle" style="width:80px;height:80px;object-fit:cover;">

                    <h5 class="fw-bold mb-1">{{ $user->fullname }}</h5>
                    <p class="text-muted mb-2" style="font-size:14px;">{{ $user->email }}</p>

                    {{-- Role Badge --}}
                    <span class="badge rounded-pill px-3 py-2
                        {{ $user->role === 'admin' ? 'text-bg-warning' : 'text-bg-info' }}">
                        <i class="bi {{ $user->role === 'admin' ? 'bi-shield-fill' : 'bi-person-workspace' }} me-1"></i>
                        {{ ucfirst($user->role) }}
                    </span>

                    {{-- Status --}}
                    <div class="mt-3">
                        @if($user->isBanned())
                            <span class="badge bg-danger rounded-pill px-3 py-2">
                                <i class="bi bi-slash-circle me-1"></i> Banned
                            </span>
                        @elseif($user->is_active)
                            <span class="badge bg-success rounded-pill px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i> Active
                            </span>
                        @else
                            <span class="badge bg-secondary rounded-pill px-3 py-2">
                                <i class="bi bi-dash-circle me-1"></i> Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-lightning-fill me-2 text-primary"></i>Actions
                    </h6>
                </div>
                <div class="card-body p-3 d-grid gap-2">

                    <a href="{{ route('proprietor.users.edit', $user) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i> Edit Profile
                    </a>

                    <form action="{{ route('proprietor.users.reset-password', $user) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="btn btn-outline-secondary btn-sm w-100"
                                onclick="return confirm('Reset password for {{ $user->name }}?')">
                            <i class="bi bi-key me-1"></i> Reset Password
                        </button>
                    </form>

                    @if($user->isBanned())
                    <form action="{{ route('proprietor.users.unban', $user) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="btn btn-outline-success btn-sm w-100">
                            <i class="bi bi-unlock me-1"></i> Unban User
                        </button>
                    </form>
                    @else
                    <form action="{{ route('proprietor.users.ban', $user) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="btn btn-outline-warning btn-sm w-100"
                                onclick="return confirm('Ban {{ $user->fullname }}?')">
                            <i class="bi bi-slash-circle me-1"></i> Ban User
                        </button>
                    </form>
                    @endif

                    <hr class="my-1">

                    <form action="{{ route('proprietor.users.destroy', $user) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm w-100"
                                onclick="return confirm('Permanently delete {{ $user->name }}? This cannot be undone.')">
                            <i class="bi bi-trash me-1"></i> Delete Account
                        </button>
                    </form>

                </div>
            </div>

        </div>

        {{-- Right: Details --}}
        <div class="col-12 col-lg-8">

            {{-- Account Details --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-person-badge me-2 text-primary"></i>Account Details
                    </h6>
                </div>
                <div class="card-body p-0">
                    <dl class="mb-0">
                        <div class="row g-0 px-4 py-3 border-bottom align-items-center">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Full Name</dt>
                            <dd class="col-7 col-sm-8 mb-0 fw-semibold" style="font-size:14px;">{{ $user->fullname }}</dd>
                        </div>
                        <div class="row g-0 px-4 py-3 border-bottom align-items-center">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Email</dt>
                            <dd class="col-7 col-sm-8 mb-0" style="font-size:14px;">{{ $user->email }}</dd>
                        </div>
                        <div class="row g-0 px-4 py-3 border-bottom align-items-center">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Role</dt>
                            <dd class="col-7 col-sm-8 mb-0">
                                <span class="badge rounded-pill {{ $user->role === 'admin' ? 'text-bg-warning' : 'text-bg-info' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </dd>
                        </div>
                        <div class="row g-0 px-4 py-3 border-bottom align-items-center">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Last Login</dt>
                            <dd class="col-7 col-sm-8 mb-0 text-muted" style="font-size:14px;">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never logged in' }}
                            </dd>
                        </div>
                        <div class="row g-0 px-4 py-3 align-items-center">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Account Created</dt>
                            <dd class="col-7 col-sm-8 mb-0 text-muted" style="font-size:14px;">
                                {{ $user->created_at->format('d M Y, h:i A') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Profile Details --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-card-list me-2 text-primary"></i>Profile Details
                    </h6>
                </div>
                <div class="card-body p-0">
                    <dl class="mb-0">
                        <div class="row g-0 px-4 py-3 border-bottom align-items-center">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Phone</dt>
                            <dd class="col-7 col-sm-8 mb-0" style="font-size:14px;">
                                {{ $user->profile?->phone ?? '—' }}
                            </dd>
                        </div>
                        <div class="row g-0 px-4 py-3 border-bottom align-items-center">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Gender</dt>
                            <dd class="col-7 col-sm-8 mb-0" style="font-size:14px;">
                                {{ $user->profile?->gender ? ucfirst($user->profile->gender) : '—' }}
                            </dd>
                        </div>
                        @if ($user->role === 'staff')
                        <div class="row g-0 px-4 py-3 border-bottom align-items-center">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Staff ID</dt>
                            <dd class="col-7 col-sm-8 mb-0" style="font-size:14px;">
                                {{ $user->staff_id}}
                            </dd>
                        </div>
                            
                        @endif
                        <div class="row g-0 px-4 py-3 border-bottom align-items-center">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Date of Birth</dt>
                            <dd class="col-7 col-sm-8 mb-0" style="font-size:14px;">
                                {{ $user->profile?->date_of_birth?->format('d M Y') ?? '—' }}
                            </dd>
                        </div>
                        <div class="row g-0 px-4 py-3 border-bottom align-items-center">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Qualification</dt>
                            <dd class="col-7 col-sm-8 mb-0" style="font-size:14px;">
                                {{ $user->profile?->qualification ?? '—' }}
                            </dd>
                        </div>
                        <div class="row g-0 px-4 py-3 border-bottom align-items-center">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Employment Date</dt>
                            <dd class="col-7 col-sm-8 mb-0" style="font-size:14px;">
                                {{ $user->profile?->employment_date?->format('d M Y') ?? '—' }}
                            </dd>
                        </div>
                        <div class="row g-0 px-4 py-3 align-items-center">
                            <dt class="col-5 col-sm-4 text-muted fw-normal" style="font-size:13px;">Address</dt>
                            <dd class="col-7 col-sm-8 mb-0" style="font-size:14px;">
                                {{ $user->profile?->address ?? '—' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@extends('layouts.superadmin')

@section('content')

    <section class="container-fluid p-4">
        <x-AlertComponent />


        <div class="py-6">
            <!-- row -->
            <div class="row">
                <div class="offset-xl-3 col-xl-6 col-md-12 col-12">
                    <!-- card -->
                    <div class="card">
                        <!-- card body -->
                        <div class="card-body p-lg-6">
                            <!-- form -->
                            <form action="{{ route('store.password') }}" class="row gx-3 needs-validation" method="POST"
                                novalidate>
                                @csrf
                                <!-- form group -->
                                <div class="mb-3 col-12">
                                    <label class="form-label">
                                        Current Password
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" name="current_password" class="form-control"
                                        placeholder="Enter Old password" required />
                                    @error('current_password') <span class="text-danger text-sm">{{ $message }}</span>
                                    @enderror

                                </div>

                                <div class="mb-3 col-12">
                                    <label class="form-label">
                                        New Password
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" name="password" class="form-control" placeholder="Enter New password"
                                        required />
                                    @error('password') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3 col-12">
                                    <label class="form-label">
                                        Confirm Password
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                        placeholder="Enter New password" required />
                                    @error('password_confirmation') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-8"></div>
                                <!-- button -->
                                <div class="col-12">
                                    <button class="btn btn-primary" type="submit">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
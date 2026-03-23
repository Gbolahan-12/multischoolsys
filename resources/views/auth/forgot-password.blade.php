<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Your Password</title>
    <!-- core:css -->
    <link rel="stylesheet" href="{{ asset('admin-assets/vendors/core/core.css') }}">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- end plugin css for this page -->
    <!-- inject:css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('admin-assets/fonts/feather-font/css/iconfont.css') }}">
    <link rel="stylesheet" href="{{asset('admin-assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('admin-assets/css/demo_1/style.css') }}">
        @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    <!-- End layout styles -->
        <link rel="shortcut icon" href="{{asset('admin-assets/images/logo/multischoollogo.jpeg')}}" />

</head>

<body>
    <x-AlertComponent />
    <div class="main-wrapper">
        <div class="page-wrapper full-page">
            <div class="page-content d-flex align-items-center justify-content-center">
                <div class="row w-100 mx-0 auth-page">
                    <div class="col-md-8 col-xl-6 mx-auto">
                        <div class="card">
                            <div class="row">
                                <div class="col-md-4 pr-md-0">
                                    <div class="auth-left-wrapper">

                                    </div>
                                </div>
                                <div class="col-md-8 pl-md-0">
                                    <div class="auth-form-wrapper px-4 py-5">
                                        <a href="#" class="noble-ui-logo d-block mb-2"> <img
                                                src="{{ asset('admin-assets/images/logo/multischoollogo-removebg-preview.png') }}"
                                                width="80" alt=""> </a></a>
                                        <h5 class="text-muted font-weight-normal mb-4">Forgot your password? No problem.
                                            Just let us know your email address and we will email you a password reset
                                            link that will allow you to choose a new one.</h5>
                                        <form class="forms-sample" method="POST" action="{{ route('password.email') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Email address</label>
                                                <input type="email" class="form-control" name="email"
                                                    value="{{ old('email') }}" id="exampleInputEmail1"
                                                    placeholder="Email">
                                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                            </div>
                                            <div class="mt-3">

                                                <button type="submit"
                                                    class="btn btn-outline-primary btn-icon-text mb-2 mb-md-0">
                                                    {{-- <i class="btn-icon-prepend" data-feather="log-in"></i> --}}
                                                    Email Password Reset Link
                                                </button>
                                            </div>
                                            <a href="{{ route('register') }}" class="d-block mt-3 text-primary">Not a
                                                user? Sign
                                                up</a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- core:js -->
    <script src="{{ asset('admin-assets/vendors/core/core.js') }}"></script>
    <!-- endinject -->
    <!-- plugin js for this page -->
    <!-- end plugin js for this page -->
    <!-- inject:js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script src="{{ asset('admin-assets/vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{asset('admin-assets/js/template.js')}}"></script>
    <!-- endinject -->
    <script>
        const alert = document.querySelector('.toast');
        if (alert) {
            setTimeout(() => alert.remove(), 3000);
        }
    </script>
</body>

</html>
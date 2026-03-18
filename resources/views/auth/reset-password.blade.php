<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>NobleUI Responsive Bootstrap 4 Dashboard Template</title>
    <!-- core:css -->
    <link rel="stylesheet" href="{{ asset('admin-assets/vendors/core/core.css') }}">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- end plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('admin-assets/fonts/feather-font/css/iconfont.css') }}">
    <link rel="stylesheet" href="{{asset('admin-assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">
    <!-- endinject -->
    <!-- Layout styles -->
    
    <link rel="stylesheet" href="{{ asset('admin-assets/css/demo_1/style.css') }}">
    <!-- End layout styles -->
        @vite(['resources/scss/app.scss', 'resources/js/app.js'])

        <link rel="shortcut icon" href="{{asset('admin-assets/images/logo/multischoollogo.jpeg')}}" />
    <style>
        li {
            list-style-type: none;
        }
    </style>
</head>

<body>
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
                                                width="180px" height="180px" alt=""> </a>
                                        <h5 class="text-muted font-weight-normal mb-4">Welcome back! Log in to your
                                            <h5 class="text-muted font-weight-normal mb-4">Welcome back! Log in to your
                                                account.</h5>
                                            <form class="forms-sample" method="POST"
                                                action="{{ route('password.store') }}">
                                                @csrf
                                                <input type="hidden" name="token"
                                                    value="{{ $request->route('token') }}">

                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Email address</label>
                                                    <input type="email" class="form-control" name="email"
                                                        value="{{ old('email', $request->email) }}"
                                                        id="exampleInputEmail1" placeholder="Email">
                                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Password</label>
                                                    <input type="password" class="form-control" name="password"
                                                        id="exampleInputPassword1" autocomplete="current-password"
                                                        placeholder="New password">
                                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Password</label>
                                                    <input type="password" class="form-control"
                                                        name="password_confirmation" id="exampleInputPassword1"
                                                        autocomplete="current-password" placeholder="New password">
                                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                                </div>
                                                {{-- <div class="form-check form-check-flat form-check-primary">
                                                    <label class="form-check-label">
                                                        <a href="{{ route('password.request') }}">
                                                            Forgot Password?
                                                        </a>
                                                    </label>
                                                </div> --}}
                                                <div class="mt-3">

                                                    <button type="submit"
                                                        class="btn btn-outline-primary btn-icon-text mb-2 mb-md-0">
                                                        Reset Password
                                                    </button>
                                                </div>
                                                <a href="{{ route('register') }}" class="d-block mt-3 text-muted">Not a
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
    <script src="{{ asset('admin-assets/vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{asset('admin-assets/js/template.js')}}"></script>
    <!-- endinject -->
    <!-- custom js for this page -->
    <!-- end custom js for this page -->
    @if(session('success') || session('error'))
        <script>
            window.addEventListener("load", function () {

                if (typeof Toastify === "undefined") {
                    console.error("Toastify missing");
                    return;
                }

                @if(session('success'))
                    Toastify({
                        text: "{{ session('success') }}",
                        duration: 3500,
                        gravity: "top",
                        position: "right",
                        close: true
                    }).showToast();
                @endif

                @if(session('error'))
                    Toastify({
                        text: "{{ session('error') }}",
                        duration: 4000,
                        gravity: "top",
                        position: "right",
                        close: true,
                        style: { background: "#dc3545" }
                    }).showToast();
                @endif

                        });
        </script>
    @endif
</body>

</html>
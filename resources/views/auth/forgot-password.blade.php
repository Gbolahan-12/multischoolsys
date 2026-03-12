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
    <link rel="stylesheet" href="{{ asset('admin-assets/fonts/feather-font/css/iconfont.css') }}">
    <link rel="stylesheet" href="{{asset('admin-assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('admin-assets/css/demo_1/style.css') }}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{asset('admin-assets/images/favicon.png')}}" />
    <style>
        li {
            list-style-type: none;
        }
    </style>
</head>

<body>
        <x-AlertComponent />
    <div class="main-wrapper">
        <div class="page-wrapper full-page">
            <div class="page-content d-flex align-items-center justify-content-center">
              @if (session('status'))
    <div style="color: green;">
        {{ session('status') }}
    </div>
@endif

@error('email')
    <div style="color: red;">
        {{ $message }}
    </div>
@enderror
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
                                        <a href="#"
                                            class="noble-ui-logo d-block mb-2">MultiSchool<span>SYSTEM</span></a>
                                        <h5 class="text-muted font-weight-normal mb-4">Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.</h5>
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
</body>

</html>






{{-- <x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                    name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                href="{{ route('password.request') }}">
                {{ __('Forgot your password?') }}
            </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout> --}}

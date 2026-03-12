<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>NobleUI Responsive Bootstrap 4 Dashboard Template</title>
    <!-- core:css -->
    <link rel="stylesheet" href="{{ asset('admin-assets/vendors/core/core.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/fonts/feather-font/css/iconfont.css') }}">
    <link rel="stylesheet" href="{{asset('admin-assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">
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
                                        <a href="#" class="noble-ui-logo d-block mb-2">Multi<span>School</span></a>
                                        <h5 class="text-muted font-weight-normal mb-4">Create a free account.</h5>
                                        <form class="forms-sample" method="POST" action="{{ route('register') }}">
                                            @csrf
                                            <h5>School Details</h5>
                                            <hr>
                                            <div class="form-group">
                                                <label for="exampleInputUsername1">School name</label>
                                                <input type="text" class="form-control" value="{{ old('school_name') }}"
                                                    name="school_name" id="exampleInputUsername1" autocomplete="SchoolName"
                                                    placeholder="School name">
                                                <x-input-error :messages="$errors->get('school_name')" class="mt-2" />

                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputUsername1">School email</label>
                                                <input type="email" class="form-control" value="{{ old('school_email') }}"
                                                    name="school_email" id="exampleInputUsername1" autocomplete="SchoolEmail"
                                                    placeholder="School Email">
                                                <x-input-error :messages="$errors->get('school_email')" class="mt-2" />

                                            </div>
                                            <div class="separato">
                                            <h5>Proprietor Details</h5>
                                                <hr>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputUsername1">Fullname</label>
                                                <input type="text" class="form-control" value="{{ old('fullname') }}"
                                                    name="fullname" id="exampleInputUsername1" autocomplete="Username"
                                                    placeholder="Full name">
                                                <x-input-error :messages="$errors->get('fullname')" class="mt-2" />

                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Email address</label>
                                                <input type="email" class="form-control" value="{{ old('email') }}"
                                                    name="email" id="exampleInputEmail1" placeholder="Email">
                                                <x-input-error :messages="$errors->get('email')" class="mt-2" />

                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Password</label>
                                                <input type="password" class="form-control" name="password"
                                                    id="exampleInputPassword1" autocomplete="current-password"
                                                    placeholder="Password">
                                                <x-input-error :messages="$errors->get('password')" class="mt-2" />

                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Confirm Password</label>
                                                <input type="password" class="form-control" name="password_confirmation"
                                                    id="exampleInputPassword1" autocomplete="current-password"
                                                    placeholder="Confirm Password">
                                                <x-input-error :messages="$errors->get('password_confirmation')"
                                                    class="mt-2" />

                                            </div>
                                            <div class="form-check form-check-flat form-check-primary">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input">
                                                    Remember me
                                                </label>
                                            </div>
                                            <div class="mt-3">
                                                <button type="submit"
                                                    class="btn btn-outline-primary btn-icon-text mb-2 mb-md-0">
                                                    <i class="btn-icon-prepend" data-feather="log-in"></i>
                                                    Register Now
                                                </button>
                                            </div>
                                            <a href="{{ route('login') }}" class="d-block mt-3 text-muted">Already a
                                                user? Sign in</a>
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
    <script src="{{ asset('admin-assets/vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{asset('admin-assets/js/template.js')}}"></script>
    <script>
document.addEventListener("DOMContentLoaded", function () {

    const forms = document.querySelectorAll("form");

    forms.forEach(form => {
        form.addEventListener("submit", function () {

            const btn = form.querySelector("button[type='submit'], input[type='submit']");

            if (btn) {
                btn.disabled = true;

                const originalText = btn.innerHTML;

                btn.innerHTML = `
                    <span class="spinner-border spinner-border-sm"></span>
                    Processing...
                `;
                btn.style.cursor = 'not-allowed';

                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }, 15000);
            }

        });
    });

});
</script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Admin') — Dashboard</title>

    <link rel="stylesheet" href="{{ asset('admin-assets/vendors/core/core.css') }}">

    <link rel="stylesheet" href="{{ asset('admin-assets/vendors/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    {{-- NobleUI icon + theme CSS --}}
    <link rel="stylesheet" href="{{ asset('admin-assets/fonts/feather-font/css/iconfont.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/demo_1/style.css') }}">


    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <link rel="shortcut icon" href="{{ asset('admin-assets/images/favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <x-AlertComponent />

    <div class="main-wrapper">

        @if(auth()->user()->role === 'admin')
            @include('includes.admin.sidebar')
        @elseif(auth()->user()->role === 'proprietor')
            @include('includes.proprietor.sidebar')
        @endif

        <div class="page-wrapper">
            @include('includes.admin.header')

            <div class="page-content">
                @yield('content')
            </div>

            @include('includes.admin.footer')
        </div>
    </div>
    <script src="{{ asset('admin-assets/vendors/core/core.js') }}"></script>
    <script src="{{ asset('admin-assets/vendors/jquery.flot/jquery.flot.js') }}"></script>

    {{-- Plugin JS --}}
    <script src="{{ asset('admin-assets/vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('admin-assets/vendors/select2/select2.min.js') }}"></script>
    <script src="{{ asset('admin-assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>

    <script src="{{ asset('admin-assets/js/template.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script src="{{ asset('admin-assets/js/select2.js') }}"></script>

    <script>


        document.addEventListener("DOMContentLoaded", function () {
            const schoolSelect = document.getElementById('schoolSelect');
            const classSelect = document.getElementById('classSelect');

            if (schoolSelect && classSelect) {
                schoolSelect.addEventListener('change', function () {
                    const schoolId = this.value;
                    classSelect.innerHTML = '<option>Loading...</option>';

                    if (!schoolId) {
                        classSelect.innerHTML = '<option value="">Select Class</option>';
                        classSelect.disabled = true;
                        return;
                    }

                    fetch(`/admin/classes/${schoolId}/by-school`)
                        .then(res => res.json())
                        .then(data => {
                            classSelect.innerHTML = '<option value="">Select Class</option>';
                            if (data.length === 0) {
                                classSelect.innerHTML += '<option value="">No classes available</option>';
                                classSelect.disabled = true;
                                return;
                            }
                            data.forEach(cls => {
                                classSelect.innerHTML += `<option value="${cls.id}">${cls.name}</option>`;
                            });
                            classSelect.disabled = false;
                        })
                        .catch(() => {
                            classSelect.innerHTML = '<option value="">Error loading classes</option>';
                            classSelect.disabled = true;
                        });
                });

                classSelect.addEventListener('change', function () {
                    const classId = this.value;
                    const studentSelect = document.getElementById('studentSelect');
                    if (!studentSelect) return;

                    studentSelect.innerHTML = '<option>Loading...</option>';
                    if (!classId) {
                        studentSelect.innerHTML = '<option value="">Select Student</option>';
                        studentSelect.disabled = true;
                        return;
                    }

                    fetch(`/admin/students/${classId}/by-class`)
                        .then(res => res.json())
                        .then(data => {
                            studentSelect.innerHTML = '<option value="">Select Student</option>';
                            if (data.length === 0) {
                                studentSelect.innerHTML += '<option value="">No students available</option>';
                                studentSelect.disabled = true;
                                return;
                            }
                            data.forEach(stu => {
                                const fullName = stu.fullname || `${stu.first_name || ''} ${stu.last_name || ''}`.trim();
                                studentSelect.innerHTML += `<option value="${stu.id}">${fullName}</option>`;
                            });
                            studentSelect.disabled = false;
                        })
                        .catch(() => {
                            studentSelect.innerHTML = '<option value="">Error loading students</option>';
                            studentSelect.disabled = true;
                        });
                });
            }

            // School → class checkboxes
            const schoolSelectAlt = document.getElementById('schoolSelect');
            if (schoolSelectAlt) {
                schoolSelectAlt.addEventListener('change', function () {
                    const schoolId = this.value;
                    const classContainer = document.getElementById('classContainer');
                    if (!classContainer) return;

                    classContainer.innerHTML = 'Loading...';
                    if (!schoolId) {
                        classContainer.innerHTML = '<p class="text-muted">Select school first</p>';
                        return;
                    }

                    fetch(`/admin/get-classes/${schoolId}`)
                        .then(response => response.json())
                        .then(data => {
                            classContainer.innerHTML = '';
                            if (data.length === 0) {
                                classContainer.innerHTML = '<p class="text-danger">No classes found.</p>';
                                return;
                            }
                            data.forEach(cls => {
                                classContainer.innerHTML += `
                                    <div class="form-check">
                                        <input type="checkbox" name="classes[]" value="${cls.id}" class="form-check-input">
                                        <label class="form-check-label">${cls.name}</label>
                                    </div>`;
                            });
                        });
                });
            }
        });
    </script>

    @yield('script')
    @stack('scripts')
    <script>
        const alert = document.querySelector('.toast');
        if (alert) {
            setTimeout(() => alert.remove(), 3000);
        }
        // get current year
		const day = new Date();
		const current = day.getFullYear();
		const year = document.querySelector('.year');
		year.innerHTML = current;
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
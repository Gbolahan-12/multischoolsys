<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Staff') — Dashboard</title>
    <link rel="stylesheet" href=" {{ asset('admin-assets/vendors/core/core.css') }} ">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('admin-assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">

    <link rel="stylesheet"
        href=" {{ asset('admin-assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href=" {{ asset('admin-assets/fonts/feather-font/css/iconfont.css') }}">
    <link rel="stylesheet" href=" {{ asset('admin-assets/vendors/flag-icon-css/css/flag-icon.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/demo_1/style.css') }} ">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">

    <!-- End layout styles -->
        <link rel="shortcut icon" href="{{asset('admin-assets/images/logo/multischoollogo.jpeg')}}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <x-AlertComponent />
    <div class="main-wrapper">

        <!-- partial:partials/_sidebar.html -->
        @include('includes.staff.sidebar')

        <!-- partial -->

        <div class="page-wrapper">

            <!-- partial:partials/_navbar.html -->
            @include('includes.staff.header')


            <!-- partial -->

            <div class="page-content">
                @yield('content')
            </div>

            <!-- partial:partials/_footer.html -->
            @include('includes.staff.footer')
            <!-- partial -->

        </div>
    </div>

    <script src=" {{ asset('admin-assets/vendors/core/core.js') }} "></script>
    <script src=" {{ asset('admin-assets/vendors/jquery.flot/jquery.flot.js') }} "></script>
    <script src=" {{ asset('admin-assets/vendors/jquery.flot/jquery.flot.resize.js') }} "></script>
    <script src=" {{ asset('admin-assets/vendors/feather-icons/feather.min.js') }} "></script>
    <script src=" {{ asset('admin-assets/js/template.js') }} "></script>
    <script src="{{ asset('admin-assets/js/dashboard.js') }}"></script>
    <script src="{{ asset('admin-assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
    <!-- endinject -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const schoolSelect = document.getElementById('schoolSelect');
            const classSelect = document.getElementById('classSelect');
            const studentSelect = document.getElementById('studentSelect');
            const subjectSelect = document.getElementById('subjectSelect');

            // When school changes → load classes
            schoolSelect.addEventListener('change', function () {
                const schoolId = this.value;
                classSelect.innerHTML = '<option>Loading...</option>';
                studentSelect.innerHTML = '<option value="">Select Student</option>';
                studentSelect.disabled = true;

                if (!schoolId) {
                    classSelect.innerHTML = '<option value="">Select Class</option>';
                    classSelect.disabled = true;
                    return;
                }

                fetch(`/staff/classes/${schoolId}/by-school`)
                    .then(res => res.json())
                    .then(data => {
                        classSelect.innerHTML = '<option value="">Select Class</option>';
                        if (data.length === 0) {
                            classSelect.innerHTML += '<option value="">No classes available</option>';
                            classSelect.disabled = true;
                            studentSelect.disabled = true;
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

            // When class changes → load students
            classSelect.addEventListener('change', function () {
                const classId = this.value;
                studentSelect.innerHTML = '<option>Loading...</option>';

                if (!classId) {
                    studentSelect.innerHTML = '<option value="">Select Student</option>';
                    studentSelect.disabled = true;
                    return;
                }

                fetch(`/staff/students/${classId}/by-class`)
                    .then(res => res.json())
                    .then(data => {
                        studentSelect.innerHTML = '<option value="">Select Student</option>';

                        if (data.length === 0) {
                            studentSelect.innerHTML += '<option value="">No students available</option>';
                            studentSelect.disabled = true;
                            return;
                        }

                        data.forEach(stu => {
                            // Make sure fullname exists to avoid undefined
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

            // When school changes → load subjects
            schoolSelect.addEventListener('change', function () {
                const schoolId = this.value;
                subjectSelect.innerHTML = '<option>Loading...</option>';

                if (!schoolId) {
                    subjectSelect.innerHTML = '<option value="">Select Subject</option>';
                    subjectSelect.disabled = true;
                    return;
                }

                fetch(`/staff/subject/${schoolId}/by-school`)
                    .then(res => res.json())
                    .then(data => {
                        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
                        if (data.length === 0) {
                            subjectSelect.innerHTML += '<option value="">No subjects available</option>';
                            subjectSelect.disabled = true;
                            return;
                        }
                        data.forEach(sub => {
                            subjectSelect.innerHTML += `<option value="${sub.id}">${sub.name}</option>`;
                        });
                        subjectSelect.disabled = false;
                    })
                    .catch(() => {
                        subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
                        subjectSelect.disabled = true;
                    });
            });

        });

    </script>


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


                        // Optional safety timeout
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
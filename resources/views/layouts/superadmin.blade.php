<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>@yield('title', 'Super') Admin — Dashboard</title>

	<!-- core:css -->
	<link rel="stylesheet" href=" {{ asset('admin-assets/vendors/core/core.css') }} ">
	<!-- endinject -->
	<link rel="stylesheet" href="{{ asset('admin-assets/vendors/select2/select2.min.css') }}">

	<!-- plugin css for this page -->
	<link rel="stylesheet" href="{{ asset('admin-assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">

	<!-- end plugin css for this page -->
	<!-- inject:css -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
	<link rel="stylesheet" href=" {{ asset('admin-assets/fonts/feather-font/css/iconfont.css') }}">
	<link rel="stylesheet" href=" {{ asset('admin-assets/vendors/flag-icon-css/css/flag-icon.min.css') }} ">
	<!-- endinject -->
	<!-- Layout styles -->
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
		@include('includes.super-admin.sidebar')

		<!-- partial -->

		<div class="page-wrapper">

			<!-- partial:partials/_navbar.html -->
			@include('includes.super-admin.header')


			<!-- partial -->

			<div class="page-content">
				@yield('content')
			</div>

			<!-- partial:partials/_footer.html -->
			@include('includes.super-admin.footer')
			<!-- partial -->

		</div>
	</div>

	<script src=" {{ asset('admin-assets/vendors/core/core.js') }} "></script>
	<!-- endinject -->
	<!-- plugin js for this page -->
	<script src=" {{ asset('admin-assets/vendors/jquery.flot/jquery.flot.js') }} "></script>
	<!-- end plugin js for this page -->
	<!-- inject:js -->
	<script src=" {{ asset('admin-assets/vendors/feather-icons/feather.min.js') }} "></script>
	<script src=" {{ asset('admin-assets/js/template.js') }} "></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
		crossorigin="anonymous"></script>
	<!-- end custom js for this page -->
	<script src="{{ asset('admin-assets/vendors/select2/select2.min.js') }}"></script>

	<script src="{{ asset('admin-assets/js/select2.js') }}"></script>
	<!-- endinject -->



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
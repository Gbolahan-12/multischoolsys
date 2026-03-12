@extends('layouts.admin') {{-- Change to your layout --}}

@section('content')
<div class="container py-4">

    <!-- Page Title -->
    <div class="mb-4">
        <h2 class="fw-bold">Bulk Student Upload</h2>
        <p class="text-muted">
            Upload multiple students using a CSV file.
        </p>
    </div>

    <!-- Instructions Card -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">

            <h5 class="fw-semibold mb-3">📄 File Format Guide</h5>

            <ul class="mb-3">
                <li>File type must be <strong>.csv</strong></li>
                <li>First row must contain column headers</li>
                <li>Date format must be: <strong>MM/DD/YYYY</strong> (Example: 5/1/2010)</li>
                <li>Columns must match exactly as shown below</li>
            </ul>

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>fullname</th>
                            <th>email</th>
                            <th>school_id</th>
                            <th>class_id</th>
                            <th>dob</th>
                            <th>gender</th>
                            <th>parent_phone_no</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="mt-3">
                <small class="text-muted">
                    Example row:
                </small>
                <div class="bg-light p-2 rounded small mt-1">
                    John Doe,john@gmail.com,1,2,5/1/2010,Male,,08012345678,ADM001
                </div>
            </div>

        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Upload Card -->
    <div class="card shadow border-0">
        <div class="card-body">

            <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Select CSV File</label>
                    <input 
                        type="file" 
                        name="file" 
                        class="form-control"
                        accept=".csv"
                        required
                    >
                </div>

                <div class="d-grid d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary px-4">
                        Upload Students
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection

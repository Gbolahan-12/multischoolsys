@extends('layouts.admin')

@section('content')

<div class="card shadow-sm">
    <div class="card-body">

        <h5 class="mb-3">📌 Excel Format Instructions</h5>

        <div class="alert alert-info">
            <strong>Required Columns (in order):</strong><br>
            first_name | last_name | dob (YYYY-MM-DD) | gender (male/female) | parent_phone_no | school_id | class_id
        </div>

        <form action="{{ route('students.import.excel') }}" method="POST" enctype="multipart/form-data">
            @csrf


            <div class="mb-3">
                <label class="form-label">Upload Excel File</label>
                <input type="file" name="file" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">
                Upload Students
            </button>
        </form>

    </div>
</div>

@endsection
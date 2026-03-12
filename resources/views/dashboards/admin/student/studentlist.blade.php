@extends('layouts.admin')

@section('content')
    <x-ui.page-modal-header title="Student List" modalId="createClass" buttonText="Add New Student"
        formAction="{{ route('store-student') }}">

        {{-- Breadcrumb Slot --}}
        <x-slot:breadcrumb>
            <li class="breadcrumb-item">
                <a href="{{ route('admin-dashboard') }}">Dashboard</a>
            </li>

            <li class="breadcrumb-item active">
                Student List
            </li>
        </x-slot:breadcrumb>


        {{-- Modal Title Slot --}}
        <x-slot:modalTitle>
            Create Student
        </x-slot:modalTitle>
        @error($errors->any())

            @foreach($errors as $err)

                <div class="alert danger"> {{$err}} </div>
            @endforeach

        @enderror


        {{-- Modal Content Slot --}}
        <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="fullname" class="form-control">
            @error('fullname')
                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="school">School</label>
            <select name="school_id" id="schoolSelect" class="form-control" required>
                <option value="">Select School</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Email address</label>
            <input type="text" name="email" class="form-control">
            @error('email')
                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label>Date of Birth</label>
            <input type="date" name="dob" class="form-control">
            @error('dob')
                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label>Parent Phone no</label>
            <input type="number" name="parent_phone_no" class="form-control">
            @error('email')
                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="class">Class</label>
            <select name="class_id" id="classSelect" class="form-control" required {{ old('class_id') ? '' : 'disabled' }}>
                <option value="">Select Class</option>
                {{-- Optional: You can prefill classes if old input exists --}}
                @if(old('class_id') && old('school_id'))
                    @php
                        $oldClasses = \App\Models\School::find(old('school_id'))->classes;
                    @endphp
                    @foreach($oldClasses as $cls)
                        <option value="{{ $cls->id }}" {{ old('class_id') == $cls->id ? 'selected' : '' }}>
                            {{ $cls->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
        {{-- <div class="mb-3">
            <label>Admission No</label>
            <input type="text" name="admission_no" class="form-control">
            @error('admission_no')
            <div class="text-danger small mt-1">
                {{ $message }}
            </div>
            @enderror
        </div> --}}

    </x-ui.page-modal-header>


    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Class Table</h6>
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>School Name</th>
                                    <th>School Type</th>
                                    <th>School Address</th>
                                    <th>Student Class</th>
                                    <th>Admission No</th>
                                    <th>Admitted date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($students as $student)

                                    @if(!$student->school || !$student->class || !$student->student)

                                        <tr>
                                            <td colspan="8" class="text-center text-warning">
                                                Incomplete data for student: {{ $student->fullname }}
                                            </td>
                                        </tr>

                                    @else

                                        <tr>
                                            <td>{{ $student->fullname }}</td>
                                            <td>{{ optional($student->school)->name }}</td>
                                            <td>{{ optional($student->school)->type }}</td>
                                            <td>{{ optional($student->school)->address }}</td>
                                            <td>{{ optional($student->class)->name }}</td>
                                            <td>{{ optional($student->student)->admission_no }}</td>
                                            <td>{{ $student->created_at->format('d M Y') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn p-0" type="button" id="dropdownMenuButton3"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton3">
                                                        <a class="dropdown-item d-flex align-items-center" href="#"><i
                                                                data-feather="eye" class="icon-sm mr-2"></i> <span
                                                                class="">View</span></a>
                                                        <a class="dropdown-item d-flex align-items-center" href="#"><i
                                                                data-feather="edit-2" class="icon-sm mr-2"></i> <span
                                                                class="">Edit</span></a>
                                                        <a class="dropdown-item d-flex align-items-center" href="#"><i
                                                                data-feather="trash" class="icon-sm mr-2"></i> <span
                                                                class="">Delete</span></a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                    @endif

                                @empty

                                    <tr>
                                        <td colspan="8" class="text-center text-danger">
                                            No students available
                                        </td>
                                    </tr>

                                @endforelse



                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

{{-- @push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {

        const schoolSelect = document.getElementById('schoolSelect');
        const classSelect = document.getElementById('classSelect');

        const routeTemplate = "{{ route('classes.by.school', ':id') }}";

        schoolSelect.addEventListener('change', function () {

            let schoolId = this.value;

            console.log("Selected School:", schoolId);

            if (!schoolId) {
                classSelect.innerHTML = '<option value="">Select Class</option>';
                return;
            }

            let url = routeTemplate.replace(':id', schoolId);

            console.log("Fetching URL:", url);

            classSelect.innerHTML = '<option>Loading...</option>';
            classSelect.disabled = true;

            fetch(url)
                .then(res => {
                    console.log("Response status:", res.status);
                    return res.json();
                })
                .then(data => {

                    console.log("Returned Data:", data);

                    classSelect.innerHTML = '<option value="">Select Class</option>';

                    data.forEach(cls => {
                        classSelect.innerHTML +=
                            `<option value="${cls.id}">${cls.name}</option>`;
                    });

                    classSelect.disabled = false;
                })
                .catch(err => {
                    console.error("Fetch Error:", err);
                });
        });

    });
</script>


@endpush --}}
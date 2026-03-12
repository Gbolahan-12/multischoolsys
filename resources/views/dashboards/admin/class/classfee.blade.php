@extends('layouts.admin')

@section('content')
    <x-ui.page-modal-header title="Fee List" modalId="createClass" buttonText="Add New Class Fee"
        formAction="{{ route('class.fee.store') }}">

        {{-- Breadcrumb Slot --}}
        <x-slot:breadcrumb>
            <li class="breadcrumb-item">
                <a href="{{ route('admin-dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">
                Fees List
            </li>
        </x-slot:breadcrumb>

        {{-- Modal Title Slot --}}
        <x-slot:modalTitle>
            Create Class Fee
        </x-slot:modalTitle>

        {{-- Modal Content Slot --}}
        <div class="mb-3">
            <label>Fee Name</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}">
            @error('title')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>School</label>
            <select name="school_id" id="schoolSelect" class="form-control" required>
                <option value="">Select School</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
            @error('school_id')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Class</label>
            <select name="class_id" id="classSelect" class="form-control" required {{ old('class_id') ? '' : 'disabled' }}>
                <option value="">Select Class</option>
                @if(old('class_id') && old('school_id'))
                    @php
                        $oldClasses = \App\Models\School::find(old('school_id'))?->classes ?? [];
                    @endphp
                    @foreach($oldClasses as $cls)
                        <option value="{{ $cls->id }}" {{ old('class_id') == $cls->id ? 'selected' : '' }}>
                            {{ $cls->name }}
                        </option>
                    @endforeach
                @endif
            </select>
            @error('class_id')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Amount</label>
            <input type="text" name="amount" class="form-control" value="{{ old('amount') }}">
            @error('amount')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Term</label>
            <select name="term" class="form-control">
                <option value="">Select Term</option>
                <option value="first" {{ old('term') == 'first' ? 'selected' : '' }}>First Term</option>
                <option value="second" {{ old('term') == 'second' ? 'selected' : '' }}>Second Term</option>
                <option value="third" {{ old('term') == 'third' ? 'selected' : '' }}>Third Term</option>
            </select>
            @error('term')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Fee Type</label>
            <select name="type" class="form-control">
                <option value="">Select Type</option>
                <option value="compulsory" {{ old('type') == 'compulsory' ? 'selected' : '' }}>Compulsory</option>
                <option value="defaulter" {{ old('type') == 'defaulter' ? 'selected' : '' }}>Defaulter</option>
            </select>
            @error('type')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Session</label>
            <select name="session" class="form-control">
                <option value="">Select Session</option>
                <option value="2027" {{ old('session') == '2027/2028' ? 'selected' : '' }}>2027/2028</option>
                <option value="2026" {{ old('session') == '2026/2027' ? 'selected' : '' }}>2026/2027</option>
                <option value="2025" {{ old('session') == '2025/2026' ? 'selected' : '' }}>2025/2026</option>
            </select>
            @error('session')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

    </x-ui.page-modal-header>
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var myModal = new bootstrap.Modal(document.getElementById('createClass'));
                myModal.show();
            });
        </script>
    @endif



    <div class="col-lg-12 col-xl-12 stretch-card">
        <div class="card">
            @error($errors->any())

                @foreach($errors as $err)

                    <div class="alert danger"> {{$err}} </div>
                @endforeach

            @enderror
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-baseline mb-2">
                    <h6 class="card-title mb-0">Projects</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="pt-0">#</th>
                                <th class="pt-0">Fee Title</th>
                                <th class="pt-0">School Name</th>
                                <th class="pt-0">Created Date</th>
                                <th class="pt-0">Type</th>
                                <th class="pt-0">Assign</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fees as $fee)

                                <tr>
                                    <td>{{ $fee->id }}</td>
                                    <td>{{ $fee->title }}</td>
                                    <td>{{$fee->school->name}}</td>
                                    <td>{{$fee->created_at}}</td>
                                    <td><span class="">{{$fee->type}}</span></td>
                                    <td>Leonardo Payne</td>
                                    <td>
                                        <div class="dropdown mb-2">
                                            <button class="btn p-0" type="button" id="dropdownMenuButton7"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="icon-lg text-muted pb-3px" data-feather="more-horizontal"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton7">
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
                            @endforeach
                            @if ($fees == null)
                                <tr class="text-center">
                                    <td colspan="6">No data found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
@extends('layouts.admin')

@section('content')
    <x-ui.page-modal-header title="School List" modalId="createSchool" buttonText="Add New School"
        formAction="{{ route('school-store') }}">

        {{-- Breadcrumb Slot --}}
        <x-slot:breadcrumb>
            <li class="breadcrumb-item">
                <a href="{{ route('admin-dashboard') }}">Dashboard</a>
            </li>

            <li class="breadcrumb-item active">
                School List
            </li>
        </x-slot:breadcrumb>


        {{-- Modal Title Slot --}}
        <x-slot:modalTitle>
            Create School
        </x-slot:modalTitle>


        {{-- Modal Content Slot --}}
        <div class="mb-3">
            <label>School Name</label>
            <input type="text" name="name" class="form-control">
            @error('name')
                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label>School Address</label>
            <input type="text" name="address" class="form-control">
            @error('address')
                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="mb-3">
            <label>School Type</label>
            <select name="type" id="" class="form-control">
                <option value="primary">Primary</option>
                <option value="secondary">Secondary</option>
            </select>
            @error('type')
                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>

    </x-ui.page-modal-header>
    <div class="row">

        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">School List Table</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>School Name</th>
                                    <th>School Address</th>
                                    <th>School Type</th>
                                    <th>School Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($schools as $school)
                                    
                                <tr>
                                    <th>{{ $school->id }}</th>
                                    <td>{{ $school->name }}</td>
                                    <td>{{ $school->address }}</td>
                                    <td>{{ $school->type }}</td>
                                    <td>{{ $school->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
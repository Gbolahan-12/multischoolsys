@extends('layouts.admin')

@section('content')

    <x-ui.page-modal-header title="Class List" modalId="createClass" buttonText="Add New Class"
        formAction="{{ route('class-store') }}">

        {{-- Breadcrumb Slot --}}
        <x-slot:breadcrumb>
            <li class="breadcrumb-item">
                <a href="{{ route('admin-dashboard') }}">Dashboard</a>
            </li>

            <li class="breadcrumb-item active">
                Class List
            </li>
        </x-slot:breadcrumb>


        {{-- Modal Title Slot --}}
        <x-slot:modalTitle>
            Create Class
        </x-slot:modalTitle>


        {{-- Modal Content Slot --}}
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control">
             @error('name')
                <div class="text-danger small mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>
        <div class="form-group">
            <label>Select School</label>
            <select name="school_id" class="js-example-basic-single w-100" data-width="100%">
                @foreach ($schools as $school)
                
                <option value="{{ $school->id }}">{{$school->name}}</option>
                @endforeach
            </select>
             @error('school_id')
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
                    <h6 class="card-title">Class Table</h6>
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>Class Name</th>
                                    <th>School Name</th>
                                    <th>School Type</th>
                                    <th>School Address</th>
                                    <th>Created date</th>
                                    <th>Totale Students</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($schclasses as $class)
                                
                                <tr>
                                    <td>{{ $class->name }}</td>
                                    <td>{{ $class->school->name }}</td>
                                    <td>{{ $class->school->type }}</td>
                                    <td>{{ $class->school->address }}</td>
                                    <td>{{ $class->created_at }}</td>
                                    <td>{{ $class->students->count() }}</td>
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
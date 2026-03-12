@extends('layouts.admin')

@section('content')
    <div class="row d-flex align-center justify-content-center">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                @error($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @enderror
                <div class="card-body">
                    <h4 class="card-title">Make Payment</h4>
                    <form class="cmxform" id="signupForm" method="post" action="{{ route('admin.payment.store') }}">
                        @csrf
                        <fieldset>
                            <div class="form-group">
                                <select class="js-example-basic-single w-100" name="student_id" data-width="100%">
                                    <option value="">Select Student</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->fullname }}</option>
                                    @endforeach
                                </select>
                                 @error('student_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                @if ($fees->isEmpty())
                                <select disabled>

                                    <option>No Fee Available</option>    
                                </select>
                                @else
                                <select class="js-example-basic-single w-100" name="fee_id" data-width="100%">
                                    <option value="">Select Fee</option>
                                    
                                    @foreach($fees as $fee)
                                        <option value="{{ $fee->id }}">{{ $fee->title }}</option>
                                    @endforeach
                                </select>
                                 @error('fee_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="amount_paid">Amount Paid</label>
                                <input id="amount_paid" class="form-control" name="amount_paid" type="text">
                                @error('amount_paid')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <input class="btn btn-primary" type="submit" value="Make Payment">
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
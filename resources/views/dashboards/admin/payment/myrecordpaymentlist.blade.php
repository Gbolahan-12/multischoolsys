@extends('layouts.admin')

@section('content')


<div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">My Payment Record Table</h6>
                    <form method="GET">
                        <div class="row d-flex justify-content-between align-item-center my-4">
                            <div class="form-group col-sm-6 col-md-3 col-lg-2">
                                <select class="form-control form-control-sm mb-3" name="school_id">
                                    @foreach($schools as $sch)
                                        <option value="{{$sch->id}}">{{$sch->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-6 col-md-3 col-lg-2">
                                <select name="term" class="form-control form-control-sm mb-3">
                                    <option value="first">First</option>
                                    <option value="second">Second</option>
                                    <option value="third">Third</option>
                                </select>
                            </div>
                            <div class="mb-3 form-group col-sm-6 col-md-3 col-lg-2">
                                <select name="session" id="sessionSelect" class="form-control">
                                </select>
                            </div>
                            <div class="form-group col-sm-6 col-md-3 col-lg-2">

                                <button type="submit" class="btn btn-primary btn-icon-text mb-2 mb-md-0">
                                    <i class="btn-icon-prepend" data-feather="filter"></i>Filter
                                </button>
                            </div>
                            <div class="form-group col-sm-6 col-md-3 col-lg-2">
                                
                                <a href="{{ url()->current() }}" class="text-white bg-warning btn border">
                                    Reset</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>Fee Title</th>
                                    <th>Student Class</th>
                                    <th>Student Name</th>
                                    <th>School Type</th>
                                    <th>Term</th>
                                    <th>Amount Paid</th>
                                    <th>Record Date</th>
                                    <th>Recorder</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                
                                <tr>
                                    <td>{{ $payment->fee->title }}</td>
                                    <td>{{ $payment->fee->class->name }}</td>
                                    <td>{{ $payment->student->fullname }}</td>
                                    <td>{{ $payment->student->school->type }}</td>
                                    <td>{{ $payment->fee->term }}</td>
                                    <td>{{ $payment->amount_paid }}</td>
                                    <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $payment->recorded_by == Auth::id() ? 'You' : $payment->recorder->fullname }}</td>
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

@section('script')

<script>

    function generateSessions() {
			const select = document.getElementById("sessionSelect");
			const currentYear = new Date().getFullYear();
			select.innerHTML = "";
			for (let i = -2; i <= 0; i++) {
				let startYear = currentYear + i;
				let endYear = startYear + 1;

				let sessionText = startYear + "/" + endYear;

				let option = document.createElement("option");
				option.value = startYear;
				console.log(option);

				option.textContent = sessionText;
				if (i === 0) {
					option.selected = true;
				}

				select.appendChild(option);
			}
		}

		generateSessions();
</script>

@endsection
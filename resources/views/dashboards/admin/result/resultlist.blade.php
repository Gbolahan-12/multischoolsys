@extends('layouts.admin')

@section('content')

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Class Table</h6>
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
                                    <i class="btn-icon-prepend" data-feather="filter"></i> Filter
                                </button>
                            </div>
                            <div class="form-group col-sm-6 col-md-3 col-lg-2">
                                
                                <a href="{{ url()->current() }}" class="text-white bg-warning btn border">
                                    Reset</a>
                            </div>
                            <div class="form-group col-sm-6 col-md-3 col-lg-2">

                                <button type="button" class="btn btn-success btn-icon-text mb-2 mb-md-0">
                                    <i class="btn-icon-prepend" data-feather="download-cloud"></i>
                                    <a href="{{ route('results.export.pdf', request()->query()) }}" class="text-white">
                                        Export PDF</a>
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Session</th>
                                    <th>Term</th>
                                    <th>Student</th>
                                    <th>Class</th>
                                    <th>Subject</th>
                                    <th>CA1</th>
                                    <th>CA2</th>
                                    <th>Exam</th>
                                    <th>Total</th>
                                    <th>Grade</th>
                                    <th>Recored By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($results as $i => $r)

                                    <tr class="text-center">

                                        <td>{{ $results->firstItem() + $i }}</td>

                                        <td>{{ $r->session }}</td>
                                        <td>{{ $r->term }}</td>

                                        <td class="text-start">
                                            {{ $r->student->fullname ?? '-' }}
                                        </td>

                                        <td>{{ $r->class->name ?? '-' }}</td>

                                        <td class="text-start">
                                            {{ $r->subject->name ?? '-' }}
                                        </td>

                                        <td>{{ $r->ca1 ?? '-' }}</td>
                                        <td>{{ $r->ca2 ?? '-' }}</td>
                                        <td>{{ $r->exam ?? '-' }}</td>

                                        <td class="fw-bold">
                                            {{ $r->total ?? '-' }}
                                        </td>

                                        <td class="fw-bold
                                                                                    @if($r->grade == 'A') text-success
                                                                                    @elseif($r->grade == 'F') text-danger
                                                                                    @else text-primary
                                                                                    @endif">

                                            {{ $r->grade ?? '-' }}

                                        </td>
                                        <td>
                                            {{ $r->uploader->fullname ?? '-' }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="11" class="text-center text-danger">
                                            No Results Found
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

@section('script')

<script>

    function generateSessions() {
			const select = document.getElementById("sessionSelect");
			const currentYear = new Date().getFullYear();
			select.innerHTML = "";
			for (let i = -2; i <= 0; i++) {
				let startYear = currentYear + i;
				let endYear = startYear + 1;
                console.log(typeof(startYear));

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
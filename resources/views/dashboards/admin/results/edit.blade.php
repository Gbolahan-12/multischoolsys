@extends('layouts.admin')
@section('title', 'Edit Result')
@section('content')
<div class="container-fluid px-4">

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Edit Result</h4>
            <small class="text-muted">
                <strong>{{ $result->student->full_name }}</strong> &mdash;
                {{ $result->subject->name }} &mdash;
                {{ $result->schoolClass->full_name }}
            </small>
        </div>
        <a href="{{ route('admin.results.view', ['class_id' => $result->class_id, 'subject_id' => $result->subject_id]) }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12 col-md-7 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0 text-uppercase text-muted" style="font-size:12px;letter-spacing:.05em;">
                        <i class="bi bi-pencil me-2 text-primary"></i>Update Scores
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.results.update', $result) }}" method="POST">
                        @csrf @method('PUT')

                        {{-- Current total banner --}}
                        <div class="rounded-3 bg-light p-3 mb-4 text-center">
                            <small class="text-muted d-block mb-1">Current Total</small>
                            <span class="fw-bold fs-3
                                {{ match($result->grade) {
                                    'A' => 'text-success',
                                    'B' => 'text-primary',
                                    'C' => 'text-info',
                                    'D' => 'text-warning',
                                    default => 'text-danger'
                                } }}">
                                {{ $result->total_score }}
                            </span>
                            <span class="badge ms-2
                                {{ match($result->grade) {
                                    'A' => 'bg-success',
                                    'B' => 'bg-primary',
                                    'C' => 'bg-info text-dark',
                                    'D' => 'bg-warning text-dark',
                                    'E' => 'bg-secondary',
                                    default => 'bg-danger'
                                } }}">
                                {{ $result->grade }} — {{ $result->remark }}
                            </span>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-medium">
                                    CA1 Score <small class="text-muted">(max 20)</small>
                                </label>
                                <input type="number" name="ca1_score"
                                       class="form-control @error('ca1_score') is-invalid @enderror"
                                       min="0" max="20" step="0.5"
                                       value="{{ old('ca1_score', $result->ca1_score) }}">
                                @error('ca1_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium">
                                    CA2 Score <small class="text-muted">(max 20)</small>
                                </label>
                                <input type="number" name="ca2_score"
                                       class="form-control @error('ca2_score') is-invalid @enderror"
                                       min="0" max="20" step="0.5"
                                       value="{{ old('ca2_score', $result->ca2_score) }}">
                                @error('ca2_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-medium">
                                    Exam Score <small class="text-muted">(max 60)</small>
                                </label>
                                <input type="number" name="exam_score"
                                       class="form-control @error('exam_score') is-invalid @enderror"
                                       min="0" max="60" step="0.5"
                                       value="{{ old('exam_score', $result->exam_score) }}">
                                @error('exam_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- <div class="col-12">
                                <label class="form-label fw-medium">
                                    Bonus Mark <small class="text-muted">(optional)</small>
                                </label>
                                <input type="number" name="bonus_mark"
                                       class="form-control @error('bonus_mark') is-invalid @enderror"
                                       min="0" step="0.5"
                                       value="{{ old('bonus_mark', $result->bonus_mark > 0 ? $result->bonus_mark : '') }}"
                                       placeholder="Leave blank if none">
                                @error('bonus_mark')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div> --}}

                            {{-- <div class="col-12">
                                <label class="form-label fw-medium">Apply Bonus To</label>
                                <select name="bonus_component"
                                        class="form-select @error('bonus_component') is-invalid @enderror">
                                    <option value="">-- None --</option>
                                    <option value="ca1"  {{ old('bonus_component', $result->bonus_component) === 'ca1'  ? 'selected' : '' }}>CA1</option>
                                    <option value="ca2"  {{ old('bonus_component', $result->bonus_component) === 'ca2'  ? 'selected' : '' }}>CA2</option>
                                    <option value="exam" {{ old('bonus_component', $result->bonus_component) === 'exam' ? 'selected' : '' }}>Exam</option>
                                </select>
                                @error('bonus_component')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Bonus cannot push a component above its maximum.</div>
                            </div> --}}

                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle me-1"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
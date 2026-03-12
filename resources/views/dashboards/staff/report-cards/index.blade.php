@extends('layouts.staff')
@section('title', 'Report Cards')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">Report Cards</h4>
            <small class="text-muted">Generate and download student report cards by class</small>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger rounded-3">{{ session('error') }}</div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold"
                        style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                        Select Class & Term
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('staff.report-cards.preview') }}" method="GET" id="reportForm">

                        {{-- Session --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Session <span class="text-danger">*</span>
                            </label>
                            <select name="session_id" id="sessionSelect" class="form-select" required>
                                <option value="">Select Session</option>
                                @foreach($sessions as $session)
                                <option value="{{ $session->id }}"
                                    {{ optional($currentTerm)->session_id == $session->id ? 'selected' : '' }}>
                                    {{ $session->name }}
                                    @if($session->is_current) (Current) @endif
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Term --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Term <span class="text-danger">*</span>
                            </label>
                            <select name="term_id" id="termSelect" class="form-select" required>
                                <option value="">Select Session First</option>
                                @if($currentTerm)
                                <option value="{{ $currentTerm->id }}" selected>
                                    {{ ucfirst($currentTerm->name) }} Term (Current)
                                </option>
                                @endif
                            </select>
                        </div>

                        {{-- Class --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="font-size:13px;">
                                Class <span class="text-danger">*</span>
                            </label>
                            <select name="class_id" class="form-select" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->name }}
                                    {{ $class->section->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary flex-grow-1 fw-semibold mx-2">
                                <i class="bi bi-eye me-2"></i>Preview Report Cards
                            </button>
                            <button type="submit"
                                    formaction="{{ route('staff.report-cards.download') }}"
                                    class="btn btn-success fw-semibold px-3 mx-2"
                                    title="Download PDF directly">
                                <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            {{-- Grade Key --}}
            <div class="card border-0 shadow-sm rounded-3 mt-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-semibold"
                        style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;">
                        Grade Key
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0" style="font-size:13px;">
                            <thead class="table-light">
                                <tr>
                                    <th>Grade</th>
                                    <th>Score Range</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach([
                                    ['A','75 – 100','Excellent','success'],
                                    ['B','65 – 74','Very Good','primary'],
                                    ['C','55 – 64','Good','info'],
                                    ['D','45 – 54','Pass','warning'],
                                    ['E','40 – 44','Fair','secondary'],
                                    ['F','0 – 39','Fail','danger'],
                                ] as [$grade,$range,$remark,$color])
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $color }} rounded-pill">{{ $grade }}</span>
                                    </td>
                                    <td>{{ $range }}</td>
                                    <td>{{ $remark }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const termsBySessionUrl = "{{ route('staff.report-cards.terms') }}";

document.getElementById('sessionSelect').addEventListener('change', function () {
    const termSelect = document.getElementById('termSelect');
    termSelect.innerHTML = '<option value="">Loading...</option>';

    if (!this.value) {
        termSelect.innerHTML = '<option value="">Select Session First</option>';
        return;
    }

    fetch(`${termsBySessionUrl}?session_id=${this.value}`)
        .then(r => r.json())
        .then(terms => {
            termSelect.innerHTML = '<option value="">Select Term</option>';
            terms.forEach(t => {
                termSelect.innerHTML += `<option value="${t.id}">
                    ${t.name.charAt(0).toUpperCase() + t.name.slice(1)} Term
                    ${t.is_current ? '(Current)' : ''}
                </option>`;
            });
        });
});
</script>
@endsection
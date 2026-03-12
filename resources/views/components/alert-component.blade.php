@if (session('success'))
<div class="toast show" style="position: absolute; top: 10px; right: 10px; z-index: 3000;">
    <div class="toast-header">
      <strong class="me-auto">MultiSch</strong>
      <button type="button" class="ms-2 mb-1 btn-close" data-bs-dismiss="toast" aria-label="Close">
      </button>
    </div>
    <div class="toast-body text-warning">
      {{ session('success') }}
    </div>
  </div>
@endif
@if (session('error'))
<div class="toast show" style="position: absolute; top: 10px; right: 10px; z-index: 3000;">
    <div class="toast-header">
      <strong class="me-auto">MultiSch</strong>
      <button type="button" class="ms-2 mb-1 btn-close" data-bs-dismiss="toast" aria-label="Close">
      </button>
    </div>
    <div class="toast-body text-danger">
      {{ session('error') }}
    </div>
  </div>
@endif

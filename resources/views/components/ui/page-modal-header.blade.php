<div class="row">
    <div class="col-12">
        <div class="border-bottom pb-3 mb-3 d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-between">

            <div class="d-flex flex-column gap-1">
                <h1 class="mb-0 h2 fw-bold">{{ $title }}</h1>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        {{ $breadcrumb }}
                    </ol>
                </nav>
            </div>

            <div>
                <button class="btn btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#{{ $modalId }}">
                    {{ $buttonText }}
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade overflow-auto" id="{{ $modalId }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title mb-0">{{ $modalTitle }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{ $slot }}

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            Submit
                        </button>

                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

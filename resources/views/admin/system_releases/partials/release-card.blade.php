<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <div class="d-flex justify-content-between">

            <div>

                <h5 class="fw-bold text-primary mb-1">
                    {{ $release->version }}
                </h5>

                <small class="text-muted">
                    {{ $release->released_at?->format('d/m/Y H:i') }}
                </small>

            </div>

            <span class="badge bg-success">
                Produção
            </span>

        </div>

        <hr>

        {!! $release->release_notes !!}

    </div>

</div>
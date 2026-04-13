<div class="col-md-3">
    <div class="card border-secondary shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Submissões em Atraso</h6>
                    <h3 class="fw-bold mb-0">{{ $submissionCounts['late'] ?? 0 }}</h3>
                </div>
                <div class="text-secondary fs-2">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
            </div>

            <a href="{{ route('attendance.my', ['status' => 'late']) }}"
               class="stretched-link"></a>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="card border-success shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Submissões Aprovadas</h6>
                    <h3 class="fw-bold mb-0">{{ $submissionCounts['approved'] ?? 0 }}</h3>
                </div>
                <div class="text-success fs-2">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>

            <a href="{{ route('attendance.submissions.index', ['status' => 'approved']) }}"
               class="stretched-link"></a>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="card border-warning shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Submissões Pendentes</h6>
                    <h3 class="fw-bold mb-0">{{ $submissionCounts['submitted'] ?? 0 }}</h3>
                </div>
                <div class="text-warning fs-2">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>

            <a href="{{ route('attendance.submissions.index', ['status' => 'submitted']) }}"
               class="stretched-link"></a>
        </div>
    </div>
</div>

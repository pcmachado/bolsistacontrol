<div class="col-md-3">
    <div class="card border-danger shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Submissões Rejeitadas</h6>
                    <h3 class="fw-bold mb-0">{{ $submissionCounts['rejected'] ?? 0 }}</h3>
                </div>
                <div class="text-danger fs-2">
                    <i class="bi bi-x-circle"></i>
                </div>
            </div>

            <a href="{{ route('attendance.submissions.index', ['status' => 'rejected']) }}"
               class="stretched-link"></a>
        </div>
    </div>
</div>

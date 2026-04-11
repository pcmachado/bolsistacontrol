<div class="card shadow-sm border-start border-4 border-{{ $color }} h-100">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <span class="text-muted">{{ $title }}</span>
            <i class="bi {{ $icon }} text-{{ $color }} fs-4"></i>
        </div>

        <h4 class="fw-bold mt-2">
            R$ {{ $value }}
        </h4>

        @isset($percent)
            <div class="small text-muted">
                {{ number_format($percent, 1) }}%
            </div>
        @endisset
    </div>
</div>
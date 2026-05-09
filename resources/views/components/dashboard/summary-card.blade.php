@props([
    'title',
    'value',
    'icon' => null,
    'tone' => 'primary',
    'subtitle' => null
])

<div class="card summary-card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">

            <div>
                <div class="text-muted small mb-2">{{ $title }}</div>

                <div class="value">
                    {{ $value }}
                </div>

                @if($subtitle)
                    <small class="text-muted">{{ $subtitle }}</small>
                @endif
            </div>

            @if($icon)
                <span class="icon tone-{{ $tone }}">
                    <i class="bi {{ $icon }}"></i>
                </span>
            @endif

        </div>
    </div>
</div>
@props(['title', 'subtitle' => null])

<section class="dashboard-hero">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
        <div>
            <h2 class="fw-bold mb-2">{{ $title }}</h2>

            @if($subtitle)
                <p class="text-muted mb-0">{{ $subtitle }}</p>
            @endif
        </div>

        {{ $actions ?? '' }}
    </div>
</section>
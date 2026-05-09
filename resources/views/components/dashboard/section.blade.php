@props(['title', 'subtitle' => null])

<section>
    <div class="section-title">
        <div>
            <h4 class="mb-1">{{ $title }}</h4>

            @if($subtitle)
                <small class="text-muted">{{ $subtitle }}</small>
            @endif
        </div>
    </div>

    {{ $slot }}
</section>
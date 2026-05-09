@props(['title', 'id'])

<div class="card section-card h-100">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <strong>{{ $title }}</strong>
    </div>

    <div class="card-body px-4 pb-4">
        <canvas id="{{ $id }}"></canvas>
    </div>
</div>
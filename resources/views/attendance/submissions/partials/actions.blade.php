@php
    $showRoute = $mode === 'self'
        ? 'my-attendance.submissions.show'
        : 'attendance.submissions.show';

    $submitRoute = $mode === 'self'
        ? 'my-attendance.submissions.submit'
        : 'attendance.submissions.submit';
@endphp

<div class="btn-group">
    <a href="{{ route($showRoute, $row) }}"
       class="btn btn-sm btn-primary"
       title="Visualizar">
        <i class="bi bi-eye"></i>
    </a>

    @if($mode === 'self')
        @can('submit', $row)
            <form method="POST" action="{{ route($submitRoute, $row) }}">
                @csrf
                <button class="btn btn-sm btn-warning" title="Enviar para homologação">
                    <i class="bi bi-send"></i>
                </button>
            </form>
        @endcan
    @endif
</div>

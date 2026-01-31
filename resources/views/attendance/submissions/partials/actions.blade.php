<div class="btn-group">

    <a href="{{ route('attendance.submissions.show', $row) }}"
       class="btn btn-sm btn-primary"
       title="Visualizar">
        <i class="bi bi-eye"></i>
    </a>

    @can('submit', $row)
        <form method="POST"
              action="{{ route('attendance.submissions.submit', $row) }}">
            @csrf
            <button class="btn btn-sm btn-warning"
                    title="Enviar para homologação">
                <i class="bi bi-send"></i>
            </button>
        </form>
    @endcan

</div>

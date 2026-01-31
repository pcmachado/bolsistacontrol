<div class="btn-group btn-group-sm">

    {{-- Visualizar sempre --}}
    <a href="{{ route('attendance.show', $record) }}"
       class="btn btn-outline-secondary"
       title="Visualizar">
        <i class="bi bi-eye"></i>
    </a>

    @if (is_null($record->attendance_submission_id))
        @can('update', $record)
            <a href="{{ route('attendance.edit', $record) }}"
               class="btn btn-outline-warning"
               title="Editar">
                <i class="bi bi-pencil"></i>
            </a>
        @endcan

        @can('delete', $record)
            <form method="POST"
                  action="{{ route('attendance.destroy', $record) }}">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger"
                        onclick="return confirm('Excluir este registro?')">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        @endcan
    @else
        <button class="btn btn-outline-secondary" disabled title="Registro enviado">
            <i class="bi bi-lock"></i>
        </button>
    @endif

</div>

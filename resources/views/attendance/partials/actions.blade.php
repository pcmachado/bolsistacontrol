<div class="btn-group btn-group-sm">

    {{-- Visualizar sempre --}}
    <a href="{{ route('attendance.show', $record) }}"
       class="btn btn-outline-secondary"
       title="Visualizar">
        <i class="bi bi-eye"></i>
    </a>

    @php
        $isClosed = \App\Models\FinancialClosure::isClosed(
            $record->scholarshipHolder->unit_id,
            $record->date->month,
            $record->date->year
        );
    @endphp

    @if ($record->isEditable() && !$isClosed)

        @can('update', $record)
            <a href="{{ route('attendance.edit', $record) }}"
               class="btn btn-outline-warning"
               title="Editar">
                <i class="bi bi-pencil"></i>
            </a>
        @endcan

        @can('delete', $record)
            <form method="POST"
                  action="{{ route('attendance.destroy', $record) }}"
                  onsubmit="return confirm('Excluir este registro?')">
                @csrf
                @method('DELETE')

                <button class="btn btn-outline-danger">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        @endcan

    @else
        <button class="btn btn-outline-secondary" disabled
            title="{{ $isClosed ? 'Período fechado' : 'Registro bloqueado para edição' }}">
            <i class="bi bi-lock"></i>
        </button>
    @endif

</div>
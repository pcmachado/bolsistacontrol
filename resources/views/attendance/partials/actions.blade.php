<div class="btn-group" role="group">

    {{-- Editar --}}
    @can('update', $record)
        <a href="{{ route('attendance.edit', $record) }}" class="btn btn-sm btn-warning">
            <i class="bi bi-pencil"></i> Editar
        </a>
    @endcan

    {{-- Excluir --}}
    @can('delete', $record)
        <form action="{{ route('attendance.destroy', $record) }}" method="POST" style="display:inline">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger"
                    onclick="return confirm('Tem certeza que deseja excluir este registro?')">
                <i class="bi bi-trash"></i> Excluir
            </button>
        </form>
    @endcan

    {{-- Submeter --}}
    @can('submit', $record)
        <form action="{{ route('attendance.submit', $record) }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-info">
                <i class="bi bi-upload"></i> Enviar
            </button>
        </form>
    @endcan

    {{-- Aprovar (somente coordenadores/admin) --}}
    @can('approve', $record)
        <form action="{{ route('admin.homologations.approve', $record) }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-success">
                <i class="bi bi-check2"></i> Aprovar
            </button>
        </form>
    @endcan

    {{-- Rejeitar (somente coordenadores/admin) --}}
    @can('reject', $record)
        <form action="{{ route('admin.homologations.reject', $record) }}" method="POST" style="display:inline">
            @csrf
            <input type="hidden" name="reason" value="Motivo da rejeição">
            <button type="submit" class="btn btn-sm btn-secondary">
                <i class="bi bi-x-circle"></i> Rejeitar
            </button>
        </form>
    @endcan

</div>
{{-- Badge de aviso para registros rejeitados --}}
@if($record->status === 'rejected' && $record->rejected_at && now()->diffInDays($record->rejected_at) <= 7)
    <div class="mt-1">
        <span class="badge bg-warning">
            Ajustes liberados até {{ $record->rejected_at->addDays(7)->format('d/m/Y') }}
        </span>
    </div>
@endif
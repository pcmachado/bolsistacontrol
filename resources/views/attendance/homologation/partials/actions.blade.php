<div class="btn-group" role="group">

    {{-- Aprovar --}}
    @can('approve', $record)
        <form action="{{ route('admin.homologations.approve', $record) }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-success"
                    onclick="return confirm('Confirma homologar este registro?')">
                <i class="bi bi-check2"></i> Aprovar
            </button>
        </form>
    @endcan

    {{-- Rejeitar --}}
    @can('reject', $record)
        <form action="{{ route('admin.homologations.reject', $record) }}" method="POST" style="display:inline">
            @csrf
            <input type="hidden" name="reason" value="Motivo da rejeição">
            <button type="submit" class="btn btn-sm btn-danger"
                    onclick="return confirm('Confirma rejeitar este registro?')">
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
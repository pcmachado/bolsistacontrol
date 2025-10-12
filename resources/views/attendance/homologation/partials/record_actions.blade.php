{{-- Botões de ação para cada registro de frequência --}}
<div class="btn-group" role="group">
    {{-- Editar: permitido se for rascunho ou rejeitado dentro do prazo de 7 dias --}}
    @if(app(\App\Services\AttendanceRecordService::class)->isEditable($record))
        <a href="{{ route('attendance.edit', $record) }}" class="btn btn-sm btn-warning">
            <i class="bi bi-pencil"></i>
        </a>
    @endif

    {{-- Enviar para homologação: só em rascunho --}}
    @if($record->status === 'draft')
        <form action="{{ route('attendance.submit', $record) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-success">
                <i class="bi bi-send"></i> Enviar
            </button>
        </form>
    @endif
</div>

{{-- Badge de aviso para registros rejeitados --}}
@if($record->status === 'rejected' && $record->rejected_at && now()->diffInDays($record->rejected_at) <= 7)
    <div class="mt-1">
        <span class="badge bg-warning">
            Ajustes liberados até {{ $record->rejected_at->addDays(7)->format('d/m/Y') }}
        </span>
    </div>
@endif

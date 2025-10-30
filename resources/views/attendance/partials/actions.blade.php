<div class="btn-group" role="group">

    {{-- Editar --}}
    @can('update', $attendanceRecord)
        @if($attendanceRecord->isEditable())
            <a href="{{ route('attendance.edit', $attendanceRecord) }}" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
        @else
            <button class="btn btn-sm btn-secondary" disabled>
                <i class="bi bi-pencil"></i> Editar
            </button>
        @endif
    @endcan

    {{-- Excluir --}}
    @can('delete', $attendanceRecord)
        @if($attendanceRecord->status === \App\Models\AttendanceRecord::STATUS_DRAFT)
            <form action="{{ route('attendance.destroy', $attendanceRecord) }}" method="POST" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger"
                        onclick="return confirm('Tem certeza que deseja excluir este registro?')">
                    <i class="bi bi-trash"></i> Excluir
                </button>
            </form>
        @endif
    @endcan

    {{-- Submeter --}}
    @can('submit', $attendanceRecord)
        @if(in_array($attendanceRecord->status, [\App\Models\AttendanceRecord::STATUS_DRAFT, \App\Models\AttendanceRecord::STATUS_REJECTED]))
            <form action="{{ route('attendance.submit', $attendanceRecord) }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-info">
                    <i class="bi bi-upload"></i> Enviar
                </button>
            </form>
        @else
            <button class="btn btn-sm btn-secondary" disabled>
                <i class="bi bi-upload"></i> Enviar
            </button>
        @endif
    @endcan

    {{-- Aprovar (somente coordenadores/admin) --}}
    @can('manage_attendances', $attendanceRecord)
        @if($attendanceRecord->status === \App\Models\AttendanceRecord::STATUS_SUBMITTED)
            <form action="{{ route('admin.homologations.approve', $attendanceRecord) }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="bi bi-check2"></i> Aprovar
                </button>
            </form>
        @endif
    @endcan

    {{-- Rejeitar (somente coordenadores/admin) --}}
    @can('manage_attendances', $attendanceRecord)
        @if($attendanceRecord->status === \App\Models\AttendanceRecord::STATUS_SUBMITTED)
            <form action="{{ route('admin.homologations.reject', $attendanceRecord) }}" method="POST" style="display:inline">
                @csrf
                <input type="hidden" name="reason" value="Motivo da rejeição">
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="bi bi-x-circle"></i> Rejeitar
                </button>
            </form>
        @endif
    @endcan

</div>

{{-- Badge de aviso para registros rejeitados --}}
@if($attendanceRecord->status === \App\Models\AttendanceRecord::STATUS_REJECTED && $attendanceRecord->rejected_at && now()->diffInDays($attendanceRecord->rejected_at) <= 7)
    <div class="mt-1">
        <span class="badge bg-warning">
            Ajustes liberados até {{ $attendanceRecord->rejected_at->addDays(7)->format('d/m/Y') }}
        </span>
    </div>
@endif

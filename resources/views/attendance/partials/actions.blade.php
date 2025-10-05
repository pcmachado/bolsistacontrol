@php
    $isCoordinator = auth()->user()->hasRole('coordenador_adjunto');
@endphp

<div class="flex gap-2">
    @if($isCoordinator && $record->status === 'pending')
        <form method="POST" action="{{ route('admin.homologations.homologate', $record->id) }}">
            @csrf
            <button class="btn btn-success">Homologar</button>
        </form>

        <button class="btn btn-danger" data-toggle="modal" data-target="#rejectModal-{{ $record->id }}">
            Rejeitar
        </button>

        @include('components.attendance.reject_modal', ['record' => $record])
    @else
        @if(in_array($record->status, ['draft', 'rejected']))
            <a href="{{ route('scholarship_holder.attendance.edit', $record->id) }}" class="btn btn-primary">Editar</a>
        @endif

        @if($record->status === 'draft')
            <form method="POST" action="{{ route('scholarship_holder.attendance.send', $record->id) }}">
                @csrf
                <button class="btn btn-warning">Enviar</button>
            </form>
        @endif
    @endif
</div>

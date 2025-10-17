@extends('layouts.app')

@section('title', 'Detalhes do Registro de Frequência')

@section('content')
<div class="container">
    <h3 class="mb-4">
        <i class="bi bi-file-earmark-text me-2"></i> Detalhes do Registro
    </h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <dl class="row">
                {{-- Data --}}
                <dt class="col-sm-3">Data</dt>
                <dd class="col-sm-9">{{ $attendanceRecord->date?->format('d/m/Y') ?? '-' }}</dd>

                {{-- Horários --}}
                <dt class="col-sm-3">Entrada</dt>
                <dd class="col-sm-9">{{ $attendanceRecord->start_time ?? '-' }}</dd>

                <dt class="col-sm-3">Saída</dt>
                <dd class="col-sm-9">{{ $attendanceRecord->end_time ?? '-' }}</dd>

                {{-- Horas --}}
                <dt class="col-sm-3">Horas Trabalhadas</dt>
                <dd class="col-sm-9">{{ $attendanceRecord->hours ?? '-' }}</dd>

                {{-- Observações --}}
                <dt class="col-sm-3">Atividades / Observações</dt>
                <dd class="col-sm-9">{{ $attendanceRecord->observation ?? '-' }}</dd>

                {{-- Status --}}
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    @switch($attendanceRecord->status)
                        @case('draft') <span class="badge bg-secondary">Rascunho</span> @break
                        @case('submitted') <span class="badge bg-info">Enviado</span> @break
                        @case('approved') <span class="badge bg-success">Homologado</span> @break
                        @case('rejected') <span class="badge bg-danger">Rejeitado</span> @break
                        @default <span class="badge bg-dark">{{ ucfirst($attendanceRecord->status) }}</span>
                    @endswitch
                </dd>

                {{-- Bolsista --}}
                <dt class="col-sm-3">Bolsista</dt>
                <dd class="col-sm-9">
                    {{ $attendanceRecord->scholarshipHolder->name ?? 'Não informado' }}
                    <br>
                    <small class="text-muted">
                        Usuário: {{ $attendanceRecord->scholarshipHolder->user->name ?? '-' }}
                    </small>
                </dd>

                {{-- Unidades --}}
                <dt class="col-sm-3">Unidades</dt>
                <dd class="col-sm-9">
                    @if($attendanceRecord->scholarshipHolder && $attendanceRecord->scholarshipHolder->units->count())
                        <ul class="list-unstyled mb-0">
                            @foreach($attendanceRecord->scholarshipHolder->units as $unit)
                                <li><i class="bi bi-building me-1"></i> {{ $unit->name }}</li>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-muted">Nenhuma unidade associada</span>
                    @endif
                </dd>

                {{-- Homologação --}}
                @if($attendanceRecord->status === 'approved')
                    <dt class="col-sm-3">Homologado por</dt>
                    <dd class="col-sm-9">{{ $attendanceRecord->approver?->name ?? '-' }}</dd>
                @elseif($attendanceRecord->status === 'rejected')
                    <dt class="col-sm-3">Motivo da Rejeição</dt>
                    <dd class="col-sm-9">{{ $attendanceRecord->rejection_reason ?? '-' }}</dd>
                @endif
            </dl>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-between">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>

        {{-- Botões condicionados à Policy --}}
        <div>
            @can('update', $attendanceRecord)
                <a href="{{ route('attendance.edit', $attendanceRecord) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar
                </a>
            @endcan

            @can('delete', $attendanceRecord)
                <form action="{{ route('attendance.destroy', $attendanceRecord) }}" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Excluir
                    </button>
                </form>
            @endcan

            @can('submit', $attendanceRecord)
                <form action="{{ route('attendance.submit', $attendanceRecord) }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-upload"></i> Enviar
                    </button>
                </form>
            @endcan
        </div>
    </div>
</div>
@endsection

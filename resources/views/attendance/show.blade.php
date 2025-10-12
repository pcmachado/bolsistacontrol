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
                <dd class="col-sm-9">{{ $attendanceRecord->date ? $attendanceRecord->date->format('d/m/Y') : '-' }}</dd>

                {{-- Horas --}}
                <dt class="col-sm-3">Horas Trabalhadas</dt>
                <dd class="col-sm-9">{{ $attendanceRecord->hours }}</dd>

                {{-- Observações --}}
                <dt class="col-sm-3">Atividades / Observações</dt>
                <dd class="col-sm-9">{{ $attendanceRecord->observation ?? '-' }}</dd>

                {{-- Status --}}
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    @switch($attendanceRecord->status)
                        @case('draft')
                            <span class="badge bg-secondary">Rascunho</span>
                            @break
                        @case('submitted')
                            <span class="badge bg-info">Enviado</span>
                            @break
                        @case('approved')
                            <span class="badge bg-success">Homologado</span>
                            @break
                        @case('rejected')
                            <span class="badge bg-danger">Rejeitado</span>
                            @break
                        @default
                            <span class="badge bg-dark">{{ ucfirst($attendanceRecord->status) }}</span>
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
            </dl>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-between">
        <a href="{{ route('attendance.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>

        {{-- Se for editável, mostra botão de edição --}}
        @if(app(\App\Services\AttendanceRecordService::class)->isEditable($attendanceRecord))
            <a href="{{ route('attendance.edit', $attendanceRecord) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
        @endif
    </div>
</div>
@endsection

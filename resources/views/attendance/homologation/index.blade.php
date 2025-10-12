@extends('layouts.app')

@section('title', 'Homologação de Frequências')

@section('content')
<h3>Registros Pendentes de Homologação</h3>

{{-- Filtros --}}
<form method="GET" class="row g-3 mb-3">
    @if(auth()->user()->hasRole('Coordenador Geral'))
        <div class="col-md-4">
            <label for="unit_id" class="form-label">Unidade</label>
            <select name="unit_id" id="unit_id" class="form-select">
                <option value="">Todas</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @else
        <div class="col-md-4">
            <label class="form-label">Unidade</label>
            <input type="text" class="form-control"
                   value="{{ auth()->user()->unit?->name ?? 'Sem unidade' }}" disabled>
        </div>
    @endif

    <div class="col-md-2 align-self-end">
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </div>
</form>

{{-- Tabela de Registros --}}

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Bolsista</th>
            <th>Unidade</th>
            <th>Data</th>
            <th>Horas</th>
            <th>Observação</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{ $record->scholarshipHolder->name }}</td>
            <td>{{ $record->scholarshipHolder->unit->name }}</td>
            <td>{{ $record->date->format('d/m/Y') }}</td>
            <td>{{ $record->hours }}</td>
            <td>{{ $record->observation }}</td>
            <td>
                <form action="{{ route('admin.homologations.approve', $record) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi bi-check-circle"></i> Aprovar
                    </button>
                </form>

                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $record->id }}">
                    <i class="bi bi-x-circle"></i> Rejeitar
                </button>

                <!-- Modal de Recusa -->
                <div class="modal fade" id="rejectModal{{ $record->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <form action="{{ route('admin.homologations.reject', $record) }}" method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Recusar Registro</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <textarea name="reason" class="form-control" placeholder="Informe o motivo da recusa" required></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-danger">Confirmar Recusa</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Fim Modal -->
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

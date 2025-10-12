@extends('layouts.app')

@section('title', 'Registros Pendentes')

@section('content')
<div class="container">
    <h3 class="mb-4">
        <i class="bi bi-hourglass-split me-2"></i> Registros Pendentes
    </h3>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($pendingRecords->isEmpty())
                <p class="text-muted">Nenhum registro pendente encontrado.</p>
            @else
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Data</th>
                            <th>Horas</th>
                            <th>Observações</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRecords as $record)
                            <tr>
                                <td>{{ $record->date->format('d/m/Y') }}</td>
                                <td>{{ $record->hours }}</td>
                                <td>{{ Str::limit($record->observation, 50) }}</td>
                                <td>
                                    <span class="badge bg-info">Pendente</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('attendance-records.show', $record) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Ver Detalhes
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection

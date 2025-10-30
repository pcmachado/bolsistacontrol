@extends('layouts.app')

@section('title', 'Relatório Mensal da Unidade')

@section('content')

<div class="row mb-3">
    <div class="col-lg-12 d-flex justify-content-between align-items-center">
        <h3>Relatório de Frequência - {{ $month }}/{{ $year }}</h3>
        <a class="btn btn-primary" href="{{ url()->previous() }}">Voltar</a>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" class="row g-3 mb-3 mt-3">
    @if(auth()->user()->hasRole(['admin','coordenador_geral']))
        <div class="col-md-3">
            <label for="unit_id" class="form-label">Unidade</label>
            <select name="unit_id" id="unit_id" class="form-select">
                <option value="">Todas</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ ($unitId ?? '') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @elseif(auth()->user()->hasRole('coordenador_adjunto'))
        <div class="col-md-3">
            <label class="form-label">Unidade</label>
            <select name="unit_id" id="unit_id" class="form-select">
                @foreach(auth()->user()->units as $unit)
                    <option value="{{ $unit->id }}" {{ ($unitId ?? '') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @else
        {{-- Bolsista não deve ver esse relatório --}}
        <div class="alert alert-warning">
            Este relatório é restrito a coordenadores e administradores.
        </div>
    @endif

    <div class="col-md-3">
        <label for="status" class="form-label">Status</label>
        <select name="status" id="status" class="form-select">
            <option value="">Todos</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprovado</option>
            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Enviado</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeitado</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Rascunho</option>
            <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Atrasado</option>
        </select>
    </div>

    <div class="col-md-2">
        <label for="month" class="form-label">Mês</label>
        <input type="number" name="month" id="month" class="form-control" value="{{ $month }}" min="1" max="12">
    </div>

    <div class="col-md-2">
        <label for="year" class="form-label">Ano</label>
        <input type="number" name="year" id="year" class="form-control" value="{{ $year }}">
    </div>

    <div class="col-md-2 align-self-end">
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </div>

    {{-- Botões de exportação --}}
    <div class="col-md-3 align-self-end d-flex gap-2">
        <button type="submit" formaction="{{ route('admin.reports.export_pdf', $unit) }}" 
                formmethod="GET" name="export" value="pdf" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf"></i> PDF
        </button>

        <button type="submit" formaction="{{ route('admin.reports.export_excel', $unit) }}" 
                formmethod="GET" name="export" value="excel" class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> Excel
        </button>
    </div>
</form>

{{-- Tabela --}}
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Bolsista</th>
            <th>Horas Previstas</th>
            <th>Horas Cumpridas</th>
            <th>Valor Hora</th>
            <th>Total (R$)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($report as $item)
        <tr>
            <td>{{ $item['scholarshipHolder'] }}</td>
            <td>{{ $item['expected_hours'] ?? '-' }}</td>
            <td>{{ $item['totalHours'] }}</td>
            <td>R$ {{ number_format($item['hourlyRate'], 2, ',', '.') }}</td>
            <td>R$ {{ number_format($item['totalValue'], 2, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center text-muted">Nenhum registro encontrado.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection

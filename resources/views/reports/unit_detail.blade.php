@extends('layouts.app')

@section('title', 'Relatório Mensal da Unidade')

@section('content')

<div class="row mb-3">
    <div class="col-lg-12 d-flex justify-content-between align-items-center">
        <h3>Relatório de Frequência - {{ $month }}/{{ $year }}</h3>
        <a class="btn btn-primary" href="{{ url()->previous() }}">Voltar</a>
    </div>
</div>

{{-- Botões de exportação (somente admin/geral/adjunto) --}}
@canany(['admin','coordenador_geral','coordenador_adjunto'])
<form method="GET" action="{{ route('admin.homologations.report') }}" class="d-inline">
    <input type="hidden" name="month" value="{{ $month }}">
    <input type="hidden" name="year" value="{{ $year }}">
    @if(isset($unitId))
        <input type="hidden" name="unit_id" value="{{ $unitId }}">
    @endif

    <button type="submit" name="export" value="pdf" class="btn btn-danger">
        <i class="bi bi-file-earmark-pdf"></i> PDF
    </button>

    <button type="submit" name="export" value="excel" class="btn btn-success">
        <i class="bi bi-file-earmark-excel"></i> Excel
    </button>
</form>
@endcanany

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
</form>

{{-- Tabela --}}
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Bolsista</th>
            <th>Total de Horas</th>
            <th>Valor Calculado</th>
        </tr>
    </thead>
    <tbody>
        @forelse($report as $item)
        <tr>
            <td>{{ $item->scholarshipHolder->name }}</td>
            <td>{{ $item->total_hours }}</td>
            <td>R$ {{ number_format($item->total_value, 2, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center text-muted">Nenhum registro encontrado para o período.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection

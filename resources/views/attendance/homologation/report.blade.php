@extends('layouts.app')

@section('title', 'Relatório Mensal da Unidade')

@section('content')

<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h3>Relatório de Frequência - {{ $month }}/{{ $year }}</h3>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('admin.users.index') }}"> Voltar</a>
        </div>
    </div>
</div>

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

<form method="GET" class="row g-3 mb-3">
    @if(auth()->user()->hasRole('Coordenador Geral'))
        <div class="col-md-3">
            <label for="unit_id" class="form-label">Unidade</label>
            <select name="unit_id" id="unit_id" class="form-select">
                <option value="">Todas</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ $unitId == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @else
        <div class="col-md-3">
            <label class="form-label">Unidade</label>
            <input type="text" class="form-control"
                   value="{{ auth()->user()->unit?->name ?? 'Sem unidade' }}" disabled>
        </div>
    @endif
    ...
</form>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Bolsista</th>
            <th>Total de Horas</th>
            <th>Valor Calculado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($report as $item)
        <tr>
            <td>{{ $item->scholarshipHolder->name }}</td>
            <td>{{ $item->total_hours }}</td>
            <td>R$ {{ number_format($item->total_value, 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@extends('layouts.app')

@section('title', 'Relatório de Frequência da Unidade')

@section('content')
<h3>Relatório de Frequência - {{ $month }}/{{ $year }}</h3>

<form method="GET" class="row g-3 mb-3">
    <div class="col-md-3">
        <input type="number" name="month" class="form-control" value="{{ $month }}" min="1" max="12">
    </div>
    <div class="col-md-3">
        <input type="number" name="year" class="form-control" value="{{ $year }}">
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </div>
</form>

<table id="reportTable" class="table table-striped table-bordered">
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
            <td>{{ $item['scholarshipHolder'] }}</td>
            <td>{{ $item['totalHours'] }}</td>
            <td>R$ {{ number_format($item['totalValue'], 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@push('scripts')
<script>
    jQuery(document).ready(function($) {
        $('#reportTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['excel', 'pdf', 'print']
        });
    });
</script>
@endpush

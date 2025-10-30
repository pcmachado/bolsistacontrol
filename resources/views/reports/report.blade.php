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

    {{-- Botões de exportação --}}
    <div class="col-md-3 align-self-end d-flex gap-2">
        <button type="submit" formaction="{{ route('admin.reports.export_pdf') }}" 
                formmethod="GET" name="export" value="pdf" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf"></i> PDF
        </button>

        <button type="submit" formaction="{{ route('admin.reports.export_excel') }}" 
                formmethod="GET" name="export" value="excel" class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> Excel
        </button>
    </div>
</form>

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

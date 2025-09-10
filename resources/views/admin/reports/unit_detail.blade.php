@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">Detalhes da Unidade: {{ $unit->name }}</h2>

    {{-- Filtro por mês/ano --}}
    <form method="GET" class="row mb-4">
        <div class="col-md-2">
            <select name="month" class="form-control">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="year" class="form-control" value="{{ $year }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    {{-- Tabela detalhada --}}
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Bolsista</th>
                <th>Bolsa</th>
                <th>Total de Horas</th>
                <th>Valor Hora (R$)</th>
                <th>Total Receber (R$)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report as $row)
                <tr>
                    <td>{{ $row['holder'] }}</td>
                    <td>{{ $row['scholarship'] }}</td>
                    <td>{{ $row['totalHours'] }}</td>
                    <td>R$ {{ number_format($row['valuePerHour'], 2, ',', '.') }}</td>
                    <td><strong>R$ {{ number_format($row['totalValue'], 2, ',', '.') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Nenhum registro encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ route('admin.reports.monthly') }}" class="btn btn-secondary">⬅ Voltar</a>
</div>
@endsection
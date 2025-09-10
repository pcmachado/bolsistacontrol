@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Bolsistas por Unidade</h2>
        <canvas id="graficoBolsistas" class="w-full h-64"></canvas>
    </div>
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Filtros</h2>
        <div x-data="{ unidade: '' }">
            <select x-model="unidade" class="border rounded px-4 py-2 w-full">
                <option value="">Todas as Unidades</option>
                @foreach($unidades as $unidade)
                    <option value="{{ $unidade->id }}">{{ $unidade->nome }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    new Chart(document.getElementById('graficoBolsistas'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Bolsistas por Unidade',
                data: {!! json_encode($data) !!},
                backgroundColor: '#3b82f6',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
@endpush
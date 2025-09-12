{{-- resources/views/admin/dashboard.blade.php (Com Cabeçalho) --}}
@extends('layouts.app')

@section('title', 'Dashboard Administrativo')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">Dashboard</h1>

    <div class="mb-4">
        <label for="unidade" class="block text-sm font-medium text-gray-700">Filtrar por Unidade:</label>
        <select id="unidade" x-model="unidadeSelecionada" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">Todas</option>
            <template x-for="unidade in unidades" :key="unidade">
                <option :value="unidade" x-text="unidade"></option>
            </template>
        </select>
    </div>

    <canvas id="graficoBolsistas" class="w-full h-64"></canvas>
</div>

{{-- O seu script Alpine.js e Chart.js permanece o mesmo --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('graficoDashboard', () => ({
            unidadeSelecionada: '',
            unidades: @json($unidades),
            labels: @json($labels),
            data: @json($data),
            init() {
                const ctx = document.getElementById('graficoBolsistas').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.labels,
                        datasets: [{
                            label: 'Bolsistas',
                            data: this.data,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        }]
                    },
                    options: { responsive: true }
                });
            }
        }))
    });
</script>
@endsection
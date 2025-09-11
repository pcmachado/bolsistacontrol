@extends('layouts.app')

@section('content')
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
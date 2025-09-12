{{-- resources/views/admin/dashboard.blade.php (Convertido para Tailwind) --}}
@extends('layouts.app')

@section('title', 'Dashboard Administrativo')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Dashboard</h1>

    {{-- Cards de Resumo --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Total de Bolsistas</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $totalBolsistas }}</p>
                </div>
                <div class="text-blue-500">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Total de Unidades</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $totalUnidades }}</p>
                </div>
                <div class="text-green-500">
                     <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Notificações Pendentes</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $notificacoesPendentes }}</p>
                </div>
                <div class="text-yellow-500">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico --}}
    <div class="mt-8 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
         <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Bolsistas por Unidade</h2>
        <canvas id="graficoBolsistas"></canvas>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('graficoBolsistas').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Número de Bolsistas',
                    data: @json($data),
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>
@endpush
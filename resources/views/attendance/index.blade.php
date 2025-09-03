<!-- resources/views/frequencia/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-5xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Histórico de Frequência</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bolsista</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidade</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entrada</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saída</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total de Horas</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observação</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                {{-- O controller deve passar a variável $frequencias para esta view --}}
                {{-- @foreach ($frequencias as $frequencia)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $frequencia->bolsista->nome }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $frequencia->unidade->nome }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $frequencia->data->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $frequencia->hora_entrada->format('H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $frequencia->hora_saida ? $frequencia->hora_saida->format('H:i') : 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($frequencia->hora_entrada && $frequencia->hora_saida)
                                {{ Carbon\Carbon::parse($frequencia->hora_entrada)->diffInMinutes(Carbon\Carbon::parse($frequencia->hora_saida)) / 60 }}h
                            @else
                                Pendente
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $frequencia->observacao }}</td>
                    </tr>
                @endforeach --}}
                
                {{-- Exemplo de linha para visualização --}}
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">João Silva</td>
                    <td class="px-6 py-4 whitespace-nowrap">Unidade Central</td>
                    <td class="px-6 py-4 whitespace-nowrap">10/11/2023</td>
                    <td class="px-6 py-4 whitespace-nowrap">08:00</td>
                    <td class="px-6 py-4 whitespace-nowrap">12:00</td>
                    <td class="px-6 py-4 whitespace-nowrap">4h</td>
                    <td class="px-6 py-4">Reunião interna</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
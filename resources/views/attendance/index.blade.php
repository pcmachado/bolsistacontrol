@extends('layouts.app')

@section('title', 'Meu Histórico de Frequência')

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
                    <tbody>
                        @foreach($attendanceRecords as $record)
                            <tr>
                                <td>{{ $record->date }}</td>
                                <td>{{ $record->entry_time }}</td>
                                <td>{{ $record->exit_time }}</td>
                                <td>{{ $record->activity }}</td>
                                <td>
                                    <span class="badge {{ $record->status == 'homologado_geral' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                    </span>
                                </td>
                        <td class="px-6 py-4">{{ $frequencia->observacao }}</td>
                            </tr>
                @endforeach
                
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
@extends('layouts.app')

@section('title', 'Registrar Frequência')

@section('content')
<div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Registrar Frequência</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

        <form action="{{ route('frequencia.store') }}" method="POST" class="space-y-6">
                    @csrf
            
            <!-- Campo para Bolsista -->
            <div>
                <label for="bolsista_id" class="block text-sm font-medium text-gray-700">Bolsista</label>
                <select name="bolsista_id" id="bolsista_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Selecione o Bolsista</option>
                    {{-- O controller deve passar a variável $bolsistas para esta view --}}
                    @foreach($bolsistas as $bolsista)
                        <option value="{{ $bolsista->id }}">{{ $bolsista->nome }}</option>
                    @endforeach
                </select>
                    </div>

            <!-- Campo para Unidade -->
            <div>
                <label for="unidade_id" class="block text-sm font-medium text-gray-700">Unidade</label>
                <select name="unidade_id" id="unidade_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Selecione a Unidade</option>
                    {{-- O controller deve passar a variável $unidades para esta view --}}
                    @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}">{{ $unidade->nome }}</option>
                    @endforeach
                </select>
                    </div>
            
            <!-- Campo para Data -->
            <div>
                <label for="data" class="block text-sm font-medium text-gray-700">Data</label>
                <input type="date" name="data" id="data" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>

            <!-- Campos de Hora -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="hora_entrada" class="block text-sm font-medium text-gray-700">Hora de Entrada</label>
                    <input type="time" name="hora_entrada" id="hora_entrada" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                <div>
                    <label for="hora_saida" class="block text-sm font-medium text-gray-700">Hora de Saída</label>
                    <input type="time" name="hora_saida" id="hora_saida" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Campo para Observação -->
            <div>
                <label for="observacao" class="block text-sm font-medium text-gray-700">Observação</label>
                <textarea name="observacao" id="observacao" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>

            <!-- Botão de submissão -->
            <div>
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                    Salvar Registro
                </button>
            </div>
        </form>
        @endif
    </div>
@endsection
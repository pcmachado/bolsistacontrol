@extends('layouts.app')
@section('content')
<div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Cadastrar Novo Bolsista</h1>
    <form action="{{ route('bolsistas.store') }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo</label>
            <input type="text" name="nome" id="nome" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" required>
        </div>
        <div>
            <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
            <input type="text" name="cpf" id="cpf" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" required>
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" required>
        </div>
        <div>
            <label for="cargo_id" class="block text-sm font-medium text-gray-700">Cargo</label>
            <select name="cargo_id" id="cargo_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" required>
                @foreach($cargos as $cargo)
                    <option value="{{ $cargo->id }}">{{ $cargo->nome }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="unidade_id" class="block text-sm font-medium text-gray-700">Unidade de Atuação</label>
            <select name="unidade_id" id="unidade_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" required>
                @foreach($unidades as $unidade)
                    <option value="{{ $unidade->id }}">{{ $unidade->nome }} - {{ $unidade->cidade }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="carga_horaria" class="block text-sm font-medium text-gray-700">Carga Horária Mensal (em horas)</label>
            <input type="number" name="carga_horaria" id="carga_horaria" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" required>
        </div>
        <div>
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                Cadastrar Bolsista
            </button>
        </div>
    </form>
</div>
@endsection
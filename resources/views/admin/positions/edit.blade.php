@extends('layouts.app')

@section('title', 'Editar Cargo')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md dark:bg-gray-800">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Editar Cargo</h1>
        <a href="{{ route('admin.positions.index') }}" class="text-blue-600 hover:underline">Voltar à Listagem</a>
    </div>

    <form action="{{ route('admin.positions.update', $position->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="nome" :value="__('Nome do Cargo')" />
            <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $position->nome)" required autofocus />
            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('Atualizar Cargo') }}
            </x-primary-button>
        </div>
    </form>
</div>
@endsection
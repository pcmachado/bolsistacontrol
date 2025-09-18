@extends('layouts.app')

@section('title', 'Editar Bolsista')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md dark:bg-gray-800">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Editar Bolsista: {{ $scholarshipHolder->nome }}</h1>
        <a href="{{ route('admin.bolsistas.index') }}" class="text-blue-600 hover:underline">Voltar à Listagem</a>
    </div>

    <form action="{{ route('admin.bolsistas.update', $scholarshipHolder->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="nome" :value="__('Nome Completo')" />
            <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $scholarshipHolder->nome)" required autofocus />
        </div>

        <div class="mt-4">
            <x-input-label for="cpf" :value="__('CPF')" />
            <x-text-input id="cpf" class="block mt-1 w-full" type="text" name="cpf" :value="old('cpf', $scholarshipHolder->cpf)" required />
        </div>

        <div class="mt-4">
            <x-input-label for="telefone" :value="__('Telefone')" />
            <x-text-input id="telefone" class="block mt-1 w-full" type="text" name="telefone" :value="old('telefone', $scholarshipHolder->telefone)" />
        </div>
        
        <div class="mt-4">
            <x-input-label for="user_id" :value="__('E-mail do Utilizador (Login)')" />
            <select name="user_id" id="user_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id', $scholarshipHolder->user_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->email }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mt-4">
            <x-input-label for="position_id" :value="__('Cargo')" />
            <select name="position_id" id="position_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                @foreach($positions as $position)
                    <option value="{{ $position->id }}" {{ old('position_id', $scholarshipHolder->position_id) == $position->id ? 'selected' : '' }}>
                        {{ $position->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mt-4">
            <x-input-label for="units" :value="__('Unidades')" />
            <select name="units[]" id="units" multiple class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ $scholarshipHolder->units->contains($unit->id) ? 'selected' : '' }}>
                        {{ $unit->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('Atualizar Bolsista') }}
            </x-primary-button>
        </div>
    </form>
</div>
@endsection
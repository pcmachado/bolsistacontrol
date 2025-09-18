@extends('layouts.app')

@section('title', 'Editar Usuários')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md dark:bg-gray-800">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Editar Usuário: {{ $user->name }}</h1>
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline">Voltar à Listagem</a>
    </div>

    {{-- Exibição de Erros de Validação --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Oops!</strong>
            <span class="block sm:inline">Houve alguns problemas com os seus dados.</span>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="name" :value="__('Nome')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('E-mail')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        dd($units)
        <div class="mt-4">
            <x-input-label for="units" :value="__('Unidades')" />
            <select name="user_unit" id="unit_id" class=" form-control form-select block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ ($user->unit_id ?? 0) == $unit->id  ? 'selected' : '' }}>
                        {{ $unit->nome }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('units')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('Atualizar Utilizador') }}
            </x-primary-button>
        </div>
    </form>
</div>
@endsection
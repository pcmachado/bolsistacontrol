@extends('layouts.app')

@section('title', 'Gestão de Usuários')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold text-gray-800">Usuários</h1>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300">
            Adicionar Novo
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="card-body">
        {{-- O método table() vai renderizar a tag <table> com o ID e classes corretas --}}
        {!! $dataTable->table(['class' => 'stripe hover w-full text-sm text-left text-gray-500'], true) !!}
    </div>
</div>

@push('scripts')
    {{-- Extensão de Botões (JS) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    {{-- O método scripts() renderiza o JS de inicialização --}}
    {!! $dataTable->scripts() !!}
@endpush
@endsection
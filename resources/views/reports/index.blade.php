@extends('layouts.app')
@section('content')
<div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Gerar Relatório de Frequência</h1>
    <form id="relatorio-form" class="space-y-6">
        @csrf
        <div>
            <label for="unidade_id" class="block text-sm font-medium text-gray-700">Unidade</label>
            <select name="unidade_id" id="unidade_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" required>
                @foreach($unidades as $unidade)
                    <option value="{{ $unidade->id }}">{{ $unidade->nome }} - {{ $unidade->cidade }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="mes" class="block text-sm font-medium text-gray-700">Mês</label>
            <input type="number" name="mes" id="mes" min="1" max="12" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" required>
        </div>
        <div>
            <label for="ano" class="block text-sm font-medium text-gray-700">Ano</label>
            <input type="number" name="ano" id="ano" min="2000" max="{{ date('Y') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" required>
        </div>
        <div>
            <button type="button" id="gerar-relatorio-btn" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                Gerar e Baixar Relatório
            </button>
        </div>
    </form>
    <script>
        document.getElementById('gerar-relatorio-btn').addEventListener('click', async () => {
            const form = document.getElementById('relatorio-form');
            const formData = new FormData(form);
            const response = await fetch('{{ route('admin.reports.gerar') }}', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.filename) {
                window.location.href = `{{ url('relatorios/download') }}/${result.filename}`;
            } else {
                alert('Ocorreu um erro ao gerar o relatório.');
            }
        });
    </script>
</div>
@endsection
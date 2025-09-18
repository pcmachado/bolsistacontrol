@extends('layouts.app')

@section('title', 'Gestão de Unidades')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-semibold text-gray-800">Unidades</h1>
        <a href="{{ route('admin.units.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300">
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
        {!! $dataTable->table(['class' => 'table table-bordered table-striped', 'width' => '100%']) !!}
    </div>
</div>

@push('scripts')
    {{-- Extensão de Botões (JS) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    {{-- O método scripts() renderiza o JS de inicialização --}}
    {!! $dataTable->scripts() !!}


    {{-- ADICIONE ESTE SCRIPT --}}
    <script>
        // Quando o documento estiver pronto
        $(document).ready(function() {
            // Escuta o evento de submissão em qualquer formulário DENTRO da tabela
            $('#units-table').on('submit', 'form', function(e) {
                // Previne o comportamento padrão do formulário (que é recarregar a página)
                e.preventDefault();

                var form = $(this);
                var url = form.attr('action');

                // Pede uma última confirmação ao utilizador
                if (confirm('Tem a certeza que deseja excluir este item?')) {
                    // Envia o pedido AJAX
                    $.ajax({
                        type: 'POST', // O Laravel entende POST com _method=DELETE
                        url: url,
                        data: form.serialize(), // Envia os dados do formulário (inclui o token CSRF e o _method)
                        success: function(response) {
                            // Se o pedido for bem-sucedido, atualiza a tabela
                            // O window.LaravelDataTables['units-table'] é a instância da sua tabela
                            window.LaravelDataTables['units-table'].draw();

                            // Opcional: mostrar uma notificação de sucesso
                            // alert(response.message);
                        },
                        error: function(xhr) {
                            // Opcional: mostrar uma notificação de erro
                            alert('Ocorreu um erro ao tentar excluir o item.');
                        }
                    });
                }
            });
        });
    </script>
@endpush
@endsection
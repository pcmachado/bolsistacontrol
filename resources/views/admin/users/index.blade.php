@extends('layouts.app')

@section('title', 'Lista de Usuários')

@section('content')
<table id="usuarios" class="min-w-full divide-y divide-gray-200">
<table id="usuariosTable" class="min-w-full">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Unidade</th>
        </tr>
    </thead>
</table>
@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#usuarios').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('admin.usuarios.data') }}',
        columns: [
            { data: 'nome', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'unidade', name: 'unit' }
        ],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json' }
    });
});
</script>
@endpush
@endsection
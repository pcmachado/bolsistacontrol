@extends('layouts.app')

@section('title', 'Lista de Usuários')

@section('content')
<table id="usuarios" class="min-w-full divide-y divide-gray-200">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Unidade</th>
        </tr>
    </thead>
</table>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#usuarios').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('admin.users.data') }}',
        columns: [
            { data: 'name', name: 'name' },       // Ajuste conforme o retorno do controller
            { data: 'email', name: 'email' },
            { data: 'unit', name: 'unit' }        // Ajuste conforme o retorno do controller
        ],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json' }
    });
});
</script>
@endpush

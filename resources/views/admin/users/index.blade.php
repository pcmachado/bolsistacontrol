@extends('layouts.app')

@section('title', 'Lista de Usuários')

@section('content')
<div class="bg-white p-6 rounded shadow">
    <table id="usuariosTable" class="min-w-full">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Criado em</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
<script>
$(document).ready(function() {
    $('#usuariosTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.usuarios.data") }}',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'created_at', name: 'created_at' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        }
    });
});
</script>
@endpush
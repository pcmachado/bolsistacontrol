@extends('layouts.admin')

@section('content')
    <div class="p-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gerenciamento de Usuários
        </h2>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4 p-6">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-4">Adicionar Novo Usuário</a>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered table-striped" id="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Papel</th>
                        <th>Data de Criação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('admin.users.index') !!}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'role', name: 'role' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush

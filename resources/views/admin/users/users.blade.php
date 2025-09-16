@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Usuários</h1>

    <div class="card">
        <div class="card-header">Manage Users</div>
        <div class="card-body">
            {!! $dataTable->table(['class' => 'table table-bordered table-striped']) !!}
        </div>
    </div>
</div>
@endsection

@push('scripts')
        <script>
        // O código do seu arquivo "users" que chama .DataTable()
        $(document).ready(function() {
            $('#users').DataTable(); // Agora esta função existirá
        });
    </script>
    {!! $dataTable->scripts() !!}  
@endpush

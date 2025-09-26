@extends('layouts.app')

@section('title', 'Gestão de Usuários')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Gerenciamento de Usuários</h1>
    @hasanyrole('Super-Admin|coordenador_geral')
        <div class="card shadow-sm">
            <div class="card-body">
                {!! $dataTable->table(['class' => 'table table-hover table-striped table-bordered w-100'], true) !!}
            </div>
        </div>
    @else
        <div class="alert alert-danger">Você não tem permissão para visualizar esta tabela.</div>
    @endhasanyrole
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
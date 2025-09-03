<!-- resources/views/unidades/create.blade.php -->
@extends('layouts.app')
@section('content')
<div class="card shadow-sm">
    <div class="card-header"><h4>Cadastrar Nova Unidade</h4></div>
    <div class="card-body">
        <form action="{{ route('unidades.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="nome" class="form-label">Nome da Unidade</label>
                <input type="text" name="nome" id="nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="cidade" class="form-label">Cidade</label>
                <input type="text" name="cidade" id="cidade" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço</label>
                <input type="text" name="endereco" id="endereco" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Unidade</button>
        </form>
    </div>
</div>
@endsection
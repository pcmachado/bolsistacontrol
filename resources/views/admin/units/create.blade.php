<!-- resources/views/units/create.blade.php -->
@extends('layouts.app')
@section('content')
<div class="card shadow-sm">
    <div class="card-header"><h4>Cadastrar Nova Unidade</h4></div>
    <div class="card-body">
        <form action="{{ route('admin.units.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nome da Unidade</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="city" class="form-label">Cidade</label>
                <input type="text" name="city" id="city" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Endereço</label>
                <input type="text" name="address" id="address" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Unidade</button>
        </form>
    </div>
</div>
@endsection
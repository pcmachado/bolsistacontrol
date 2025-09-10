@extends('layouts.admin')

@section('title', 'Adicionar Usuário')

@section('content')
    <div class="p-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Adicionar Novo Usuário
        </h2>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4 p-6">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Papel</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="bolsista">Bolsista</option>
                        <option value="coordenador_adjunto">Coordenador Adjunto</option>
                        <option value="coordenador_geral">Coordenador Geral</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
        </div>
    </div>
@endsection
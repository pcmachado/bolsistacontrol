@extends('layouts.app')

@section('title', 'Gerenciar Perfis de Acesso (Funções)')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark">Gerenciamento de Funções e Permissões</h1>
        
        <!-- Botão de Ação -->
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary shadow-sm rounded-3">
            <i class="bi bi-plus-lg me-2"></i> Criar Nova Função
        </a>
    </div>

    <!-- 1. Quadro de Permissões Disponíveis -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-bold text-dark h5">
            Permissões Disponíveis no Sistema
        </div>
        <div class="card-body">
            @if (isset($permissions) && $permissions->count())
                <p class="text-muted small mb-3">Total de permissões: <span class="fw-bold">{{ $permissions->count() }}</span></p>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($permissions as $permission)
                        <span class="badge text-dark rounded-pill px-3 py-2 fw-normal">{{ $permission->name }}</span>
                    @endforeach
                </div>
            @else
                <div class="alert alert-warning mb-0">Nenhuma permissão cadastrada.</div>
            @endif
        </div>
    </div>

    <!-- 2. Tabela de Roles e Permissões Vinculadas -->
    <div class="card shadow-lg">
        <div class="card-header bg-white fw-bold h5 text-dark">
            Funções Cadastradas
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 5%;">#</th>
                            <th scope="col" style="width: 20%;">Nome da Função</th>
                            <th scope="col" style="width: 55%;">Permissões Vinculadas</th>
                            <th scope="col" style="width: 20%;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Simulação de loop pelas Roles --}}
                        @forelse (isset($roles) ? $roles : [] as $role)
                            <tr>
                                <th scope="row" class="fw-bold text-muted">{{ $role->id }}</th>
                                <td><span class="fw-bolder text-primary">{{ $role->name }}</span></td>
                                
                                {{-- Permissões Vinculadas --}}
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse ($role->permissions as $permission)
                                            <span class="badge text-dark rounded-pill px-3 py-1 fw-normal">{{ $permission->name }}</span>
                                        @empty
                                            <span class="text-danger small fst-italic">Nenhuma permissão atribuída.</span>
                                        @endforelse
                                    </div>
                                </td>
                                
                                {{-- Botões de Ação --}}
                                <td>
                                    <div class="btn-group" role="group">
                                        {{-- Botão Visualizar --}}
                                        <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-sm btn-info text-white rounded-start" title="Visualizar">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        {{-- Botão Editar --}}
                                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        {{-- Botão Excluir (Usando formulário para POST/DELETE) --}}
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger rounded-end" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta função?');">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Nenhuma função cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Paginação --}}
        @if (isset($roles) && $roles instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="card-footer bg-white pt-3 pb-0 border-top">
                {{-- Aqui o helper de paginação utiliza o tema Bootstrap 5 --}}
                {!! $roles->links('pagination::bootstrap-5') !!}
            </div>
        @endif
    </div>
</div>
@endsection

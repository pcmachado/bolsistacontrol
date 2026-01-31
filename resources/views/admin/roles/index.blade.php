@extends('layouts.app')

@section('title', 'Gerenciar Perfis de Acesso (Funções)')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark">Gerenciamento de Funções e Permissões</h1>
        
        @can('create', Spatie\Permission\Models\Role::class)
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary shadow-sm rounded-3">
                <i class="bi bi-plus-lg me-2"></i> Criar Nova Função
            </a>
        @endcan
    </div>

    <!-- Tabela de Roles -->
    <div class="card shadow-lg">
        <div class="card-header bg-white fw-bold h5 text-dark">
            Funções Cadastradas (Visíveis para seu Nível)
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 20%;">Nome da Função</th>
                            <th scope="col" style="width: 60%;">Permissões Vinculadas</th>
                            <th scope="col" style="width: 20%;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td>
                                    <span class="fw-bolder text-primary">{{ $role->name }}</span>
                                    @if (in_array($role->name, ['admin', 'superadmin']))
                                        <span class="badge bg-danger ms-2">Admin</span>
                                    @elseif (in_array($role->name, ['coordenador_geral', 'coordenador_adjunto_geral', 'coordenador_adjunto']))
                                        <span class="badge bg-warning text-dark ms-2">Coordenação</span>
                                    @endif
                                </td>
                                
                                <td>
                                    @if ($role->permissions->isEmpty())
                                        <span class="text-muted fst-italic small">Nenhuma permissão</span>
                                    @else
                                        @if($role->permissions->count() > 5)
                                            <span class="badge bg-secondary rounded-pill">
                                                {{ $role->permissions->count() }} permissões
                                            </span>
                                            <small class="text-muted ms-2">(Ver detalhes)</small>
                                        @else
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach ($role->permissions as $permission)
                                                    <span class="badge bg-light text-dark border rounded-pill">
                                                        {{ $permission->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                
                                <td>
                                    <div class="btn-group" role="group">
                                        {{-- Visualizar --}}
                                        <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-sm btn-info text-white" title="Visualizar">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        {{-- Editar: A Policy (role hierarchy) determina se aparece --}}
                                        @can('update', $role)
                                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-secondary" disabled title="Nível hierárquico insuficiente">
                                                <i class="bi bi-lock-fill"></i>
                                            </button>
                                        @endcan

                                        {{-- Excluir --}}
                                        @can('delete', $role)
                                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" onclick="return confirm('Confirmar exclusão?')" title="Excluir">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-4">Nenhuma função encontrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if ($roles instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="card-footer bg-white pt-3 pb-0 border-top">
                {!! $roles->links('pagination::bootstrap-5') !!}
            </div>
        @endif
    </div>
</div>
@endsection
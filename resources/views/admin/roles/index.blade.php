@extends('layouts.app')

@section('title', 'Gerenciar Perfis de Acesso (Funções)')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
        <h1 class="mb-0 text-black">Gerenciamento de Perfis de Acesso</h1>

        @can('create', Spatie\Permission\Models\Role::class)
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i> Criar Nova Função
            </a>
        @endcan
    </div>

    <div class="card mb-3 shadow-sm text-body">
        <div class="card-body">
            <p class="mb-0 text-muted">Funções cadastradas visíveis para o seu nível de acesso.</p>
        </div>
    </div>

    <div class="card shadow-sm rounded-4 border-0 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-striped w-100 mb-0">
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
                                    <span class="badge bg-secondary text-white ms-2">Nível {{ $role->level }}</span>
                                    @if (in_array($role->name, ['admin', 'superadmin']))
                                        <span class="badge bg-danger ms-2">Admin</span>
                                    @elseif (in_array($role->name, ['coordenador_geral', 'coordenador_adjunto_geral', 'coordenador_adjunto']))
                                        <span class="badge bg-warning text-dark ms-2">Coordenação</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($role->permissions->isEmpty())
                                        <span class="text-muted fst-italic small">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Nenhuma permissão
                                        </span>
                                    @else
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress" style="height: 20px; width: 100px;">
                                                @php
                                                    $totalPermissions = \Spatie\Permission\Models\Permission::count() ?: 1;
                                                    $percentage = min(100, round(($role->permissions->count() / $totalPermissions) * 100));
                                                @endphp
                                                <div class="progress-bar bg-success" role="progressbar"
                                                     style="width: {{ $percentage }}%;"
                                                     aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                    <small class="text-white">{{ $role->permissions->count() }}</small>
                                                </div>
                                            </div>
                                            <span class="badge bg-info rounded-pill" title="Total de permissões">
                                                {{ $role->permissions->count() }} permissão{{ $role->permissions->count() !== 1 ? 's' : '' }}
                                            </span>
                                        </div>
                                        <div class="mt-2">
                                            @php
                                                $categories = $role->permissions->groupBy(function($p) {
                                                    return explode('.', $p->name)[0];
                                                });
                                            @endphp
                                            @foreach ($categories->take(3) as $category => $perms)
                                                <span class="badge bg-light text-dark border rounded-pill small me-1 mb-1">
                                                    {{ ucfirst($category) }}: {{ $perms->count() }}
                                                </span>
                                            @endforeach
                                            @if($categories->count() > 3)
                                                <span class="badge bg-secondary rounded-pill small">
                                                    +{{ $categories->count() - 3 }} mais
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.roles.show', $role->id) }}" class="btn btn-sm btn-info text-white" title="Visualizar">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        @can('update', $role)
                                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-secondary" disabled title="Nível hierárquico insuficiente">
                                                <i class="bi bi-lock-fill"></i>
                                            </button>
                                        @endcan

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

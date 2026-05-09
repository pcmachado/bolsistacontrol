@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-dark">Detalhes da Função: <strong>{{ ucfirst(str_replace('_', ' ', $role->name)) }}</strong></h1>
            <small class="text-muted">Total de Permissões: {{ $permissionCount }}</small>
        </div>
        <div>
            @can('update', $role)
                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar
                </a>
            @endcan
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    @if ($permissionCount === 0)
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3">Esta função não possui permissões atribuídas.</p>
            </div>
        </div>
    @else
        <div class="row">
            <!-- Card de Resumo -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-shield-check text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h5 class="card-title">Permissões Totais</h5>
                        <p class="display-4 text-success fw-bold">{{ $permissionCount }}</p>
                        <small class="text-muted">Permissões atribuídas a esta função</small>
                    </div>
                </div>
            </div>

            <!-- Cards por Categoria -->
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Permissões por Categoria</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($rolePermissionsByCategory as $category => $permissions)
                                <div class="col-md-6 mb-3">
                                    <div class="card border-primary h-100">
                                        <div class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                                            <strong>{{ $category }}</strong>
                                            <span class="badge bg-white text-primary ms-2">{{ count($permissions) }}</span>
                                        </div>
                                        <div class="card-body p-2">
                                            <ul class="list-unstyled mb-0">
                                                @foreach ($permissions as $permission)
                                                    <li class="py-1">
                                                        <i class="bi bi-check-circle text-success me-2"></i>
                                                        <small>{{ $permission->name }}</small>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela completa de permissões -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Lista Completa de Permissões</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Permissão</th>
                            <th>Descrição</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rolePermissions as $permission)
                            <tr>
                                <td>
                                    <code>{{ $permission->name }}</code>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ ucfirst(implode(' - ', array_slice(explode('.', $permission->name), 1))) }}
                                    </small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<style>
    .card {
        border-radius: 0.5rem;
        border: none;
    }

    .card-header {
        border-radius: 0.5rem 0.5rem 0 0;
    }
</style>

@endsection
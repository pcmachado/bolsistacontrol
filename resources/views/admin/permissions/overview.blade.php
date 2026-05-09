@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark">Gerenciamento de Permissões do Sistema</h1>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Resumo de Permissões</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card border-primary text-center">
                                <div class="card-body">
                                    <h6 class="card-title">Total de Permissões</h6>
                                    <p class="display-5 text-primary fw-bold">{{ $totalPermissions }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-success text-center">
                                <div class="card-body">
                                    <h6 class="card-title">Total de Funções (Roles)</h6>
                                    <p class="display-5 text-success fw-bold">{{ $totalRoles }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-info text-center">
                                <div class="card-body">
                                    <h6 class="card-title">Funções Ativas</h6>
                                    <p class="display-5 text-info fw-bold">{{ $rolesInUse }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-warning text-center">
                                <div class="card-body">
                                    <h6 class="card-title">Permissões Atribuídas</h6>
                                    <p class="display-5 text-warning fw-bold">{{ $assignedPermissions }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Matriz de Permissões por Roles -->
    <div class="card shadow-lg mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Matriz de Permissões por Função</h5>
            <small class="text-muted">Clique em uma função para editar suas permissões</small>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th style="width: 200px;">Função</th>
                        <th style="width: 100px; text-align: center;">Permissões</th>
                        <th style="width: 100px; text-align: center;">% do Total</th>
                        <th style="text-align: center;">Categorias</th>
                        <th style="width: 150px; text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        @php
                            $permCount = $role->permissions()->count();
                            $percentage = $totalPermissions > 0 ? round(($permCount / $totalPermissions) * 100, 1) : 0;
                            $categories = $role->permissions()
                                ->get()
                                ->groupBy(function($p) { return explode('.', $p->name)[0]; })
                                ->count();
                        @endphp
                        <tr class="align-middle">
                            <td>
                                <strong class="text-primary">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</strong>
                                @if (in_array($role->name, ['admin', 'superadmin']))
                                    <span class="badge bg-danger ms-2">Sistema</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $percentage }}%;" 
                                         aria-valuenow="{{ $percentage }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                        <small class="text-white fw-bold">{{ $permCount }}</small>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge bg-info">{{ $percentage }}%</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge bg-secondary">{{ $categories }} categoria{{ $categories !== 1 ? 's' : '' }}</span>
                            </td>
                            <td style="text-align: center;">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.roles.show', $role->id) }}" 
                                       class="btn btn-sm btn-info text-white" 
                                       title="Visualizar">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    @can('update', $role)
                                        <a href="{{ route('admin.roles.edit', $role->id) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Distribuição de Permissões por Categoria -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Categorias de Permissões</h5>
                </div>
                <div class="card-body">
                    <div id="categoriesChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Distribuição por Função</h5>
                </div>
                <div class="card-body">
                    <div id="rolesChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Carregar Chart.js se disponível -->
@if(env('APP_DEBUG'))
<script>
console.log('Permissões carregadas com sucesso');
console.log('Total de Permissões:', {{ $totalPermissions }});
console.log('Total de Funções:', {{ $totalRoles }});
</script>
@endif
@endpush

<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }

    .progress {
        background-color: #e9ecef;
    }

    .card {
        border-radius: 0.5rem;
        border: none;
    }
</style>

@endsection

@extends('layouts.app')

@section('title', 'Dashboard Superadmin')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="mb-1">
                Painel Superadmin
            </h2>

            <div class="text-muted">
                Visão institucional global do sistema
            </div>
        </div>

        <div>
            <span class="badge bg-dark fs-6">
                SUPERADMIN
            </span>
        </div>

    </div>

    {{-- KPIs --}}
    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        Instituições
                    </div>

                    <div class="display-6 fw-bold">
                        {{ $stats['institutions'] ?? 0 }}
                    </div>

                </div>

            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        Projetos Ativos
                    </div>

                    <div class="display-6 fw-bold">
                        {{ $stats['projects'] ?? 0 }}
                    </div>

                </div>

            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        Bolsistas
                    </div>

                    <div class="display-6 fw-bold">
                        {{ $stats['scholarship_holders'] ?? 0 }}
                    </div>

                </div>

            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        Usuários
                    </div>

                    <div class="display-6 fw-bold">
                        {{ $stats['users'] ?? 0 }}
                    </div>

                </div>

            </div>
        </div>

    </div>

    {{-- LINHA 2 --}}
    <div class="row g-4 mb-4">

        {{-- Instituições --}}
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white">
                    <strong>
                        Instituições
                    </strong>
                </div>

                <div class="table-responsive">

                    <table class="table align-middle mb-0">

                        <thead>
                            <tr>
                                <th>Instituição</th>
                                <th>Projetos</th>
                                <th>Usuários</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($institutions as $institution)

                                <tr>

                                    <td>
                                        <strong>
                                            {{ $institution->name }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $institution->projects_count ?? 0 }}
                                    </td>

                                    <td>
                                        {{ $institution->users_count ?? 0 }}
                                    </td>

                                    <td>

                                        @if(($institution->projects_count ?? 0) > 0)

                                            <span class="badge bg-success">
                                                Ativa
                                            </span>

                                        @else

                                            <span class="badge bg-warning text-dark">
                                                Sem projetos
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="4" class="text-center py-4">
                                        Nenhuma instituição encontrada.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        {{-- usuários recentes --}}
        <div class="col-lg-4">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white">
                    <strong>
                        Usuários Recentes
                    </strong>
                </div>

                <div class="list-group list-group-flush">

                    @forelse($recentUsers as $user)

                        <div class="list-group-item">

                            <div class="fw-semibold">
                                {{ $user->name }}
                            </div>

                            <div class="small text-muted">
                                {{ $user->email }}
                            </div>

                            <div class="small mt-1">
                                {{ $user->created_at?->format('d/m/Y H:i') }}
                            </div>

                        </div>

                    @empty

                        <div class="list-group-item text-muted">
                            Nenhum usuário recente.
                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
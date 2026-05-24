@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Templates de Email</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Novo Template
                        </a>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Chave</th>
                                    <th>Projeto</th>
                                    <th>Instituição</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($templates as $template)
                                <tr>
                                    <td>{{ $template->name }}</td>
                                    <td><code>{{ $template->key }}</code></td>
                                    <td>{{ $template->project?->name ?? '-' }}</td>
                                    <td>{{ $template->institution?->name ?? '-' }}</td>
                                    <td>
                                        @if($template->active)
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.email-templates.edit', $template) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum template encontrado.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($templates->hasPages())
                <div class="card-footer">
                    {{ $templates->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
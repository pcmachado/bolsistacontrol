@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">

            <div>
                <h1 class="page-title mb-1">
                    Templates de Documentos
                </h1>

                <p class="text-muted mb-0">
                    Gerencie layouts institucionais, recibos e documentos PDF.
                </p>
            </div>

            <a href="{{ route('admin.document-templates.create') }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                Novo template

            </a>

        </div>
    </div>

    <div class="card shadow-sm border-0">

        <div class="table-responsive">

            <table class="table align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th>Template</th>
                        <th>Escopo</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($templates as $template)

                        <tr>

                            <td>
                                <div class="fw-semibold">
                                    {{ $template->name }}
                                </div>

                                <small class="text-muted">
                                    {{ $template->key }}
                                </small>
                            </td>

                            <td>

                                @if($template->unit)
                                    <span class="badge bg-info">
                                        Unidade
                                    </span>

                                @elseif($template->institution)
                                    <span class="badge bg-primary">
                                        Instituição
                                    </span>

                                @else
                                    <span class="badge bg-secondary">
                                        Global
                                    </span>
                                @endif

                            </td>

                            <td>

                                @if($template->active)
                                    <span class="badge bg-success">
                                        Ativo
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        Inativo
                                    </span>
                                @endif

                            </td>

                            <td class="text-end">

                                <div class="btn-group btn-group-sm">

                                    <a href="{{ route('admin.document-templates.show', $template) }}"
                                       class="btn btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.document-templates.edit', $template) }}"
                                       class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                Nenhum template cadastrado.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
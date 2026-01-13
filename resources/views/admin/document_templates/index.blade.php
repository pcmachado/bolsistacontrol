@extends('layouts.app')

@section('title', 'Templates de Documentos')

@section('content')
<div class="container">

    <h3 class="mb-4">
        <i class="bi bi-file-earmark-text me-2"></i>
        Templates de Documentos
    </h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Chave</th>
                        <th>Escopo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $t)
                        <tr>
                            <td>{{ $t->name }}</td>
                            <td><code>{{ $t->key }}</code></td>
                            <td>
                                @if($t->unit_id)
                                    Unidade
                                @elseif($t->institution_id)
                                    Instituição
                                @else
                                    Global
                                @endif
                            </td>
                            <td>
                                @if($t->active)
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-secondary">Inativo</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.document-templates.edit', $t) }}"
                                   class="btn btn-sm btn-primary">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection

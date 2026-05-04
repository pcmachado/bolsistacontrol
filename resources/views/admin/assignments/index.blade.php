@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="mb-4">Vínculos Operacionais</h1>

    <a href="{{ route('admin.assignments.create') }}" class="btn btn-primary mb-3">
        Novo vínculo
    </a>

    <div class="card">
        <div class="card-body">

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Tipo</th>
                        <th>Projeto</th>
                        <th>Curso</th>
                        <th>Unidade</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($assignments as $a)
                    <tr>
                        <td>{{ $a->user->name }}</td>
                        <td>{{ ucfirst($a->assignment_type) }}</td>
                        <td>{{ $a->project?->name }}</td>
                        <td>{{ $a->course?->name }}</td>
                        <td>{{ $a->unit?->name }}</td>
                        <td>
                            @if($a->active)
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-secondary">Inativo</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection
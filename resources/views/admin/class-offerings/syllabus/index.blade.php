@extends('layouts.app')

@section('title', 'Plano de Ensino')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between mb-4">
        <h2 class="fw-bold">
            <i class="bi bi-journal-bookmark me-2"></i>
            Plano de Ensino – {{ $offering->name }}
        </h2>

        <a href="{{ route('admin.class-offerings.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Voltar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Disciplina</th>
                        <th>Previsto</th>
                        <th>Ministrado</th>
                        <th>Faltam</th>
                        <th>%</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($rows as $r)
                        <tr>
                            <td>{{ $r['discipline']->name }}</td>

                            <td>{{ $r['planned'] }} h</td>
                            <td>{{ number_format($r['taught'], 2) }} h</td>
                            <td>{{ $r['remaining'] }} h</td>

                            <td>{{ $r['percent'] }}%</td>

                            <td>
                                @php
                                    $badge = [
                                        'Não iniciado'      => 'secondary',
                                        'Em andamento'      => 'info',
                                        'Quase concluído'   => 'warning',
                                        'Concluído'         => 'success',
                                        'Excedido'          => 'danger',
                                    ][$r['status']];
                                @endphp

                                <span class="badge bg-{{ $badge }}">
                                    {{ $r['status'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection

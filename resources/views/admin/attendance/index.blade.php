@extends('layouts.admin')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h4>Homologação de Registros de Frequência</h4>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($registros->isEmpty())
            <p class="text-center">Não há registros de frequência pendentes para homologação.</p>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Bolsista</th>
                            <th>Unidade</th>
                            <th>Data</th>
                            <th>Entrada</th>
                            <th>Saída</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($registros as $registro)
                        <tr>
                            <td>{{ $registro->scholarshipHolder->name }}</td>
                            <td>{{ $registro->unit->name }}</td>
                            <td>{{ $registro->date->format('d/m/Y') }}</td>
                            <td>{{ $registro->entry_time->format('H:i') }}</td>
                            <td>{{ $registro->exit_time->format('H:i') }}</td>
                            <td>
                                <form action="{{ route('admin.frequencia.homologar', $registro) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Homologar</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $registros->links() }}
        @endif
    </div>
</div>
@endsection
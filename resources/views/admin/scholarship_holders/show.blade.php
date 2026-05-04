@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">👁 Detalhes do Bolsista</h4>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('admin.users.index') }}"> Voltar</a>
        </div>
    <div class="card">
        <div class="card-body">
            <p><strong>Nome:</strong>{{ $scholarshipHolder->name }}</p>
            <p><strong>Email:</strong>{{ $scholarshipHolder->email }}</p>
            <p><strong>Status:</strong> {{ $scholarshipHolder->status }}</p>
        
            <div class="form-group">
                <strong>Unidade:</strong>
                {{ $scholarshipHolder->unit->name ?? 'Sem unidade' }}
            </div>

            <hr>

            <strong>Projetos / Funções:</strong>
            <div class="mt-2">
                @foreach($scholarshipHolder->projects as $project)
                    <span class="badge bg-primary">
                        {{ $project->name }} -
                        {{ optional($project->positions
                            ->firstWhere('id', $project->pivot->position_id))->name }}
                    </span>
                @endforeach
            </div>

        </div>
    </div>

</div>
@endsection
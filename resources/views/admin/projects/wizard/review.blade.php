@extends('layouts.project-wizard')

@section('title', 'Revisão do Projeto')

@section('wizard-content')

<h4 class="mb-4 fw-bold">Revisão Final do Projeto</h4>

<p class="text-muted mb-4">
    Confira todas as informações abaixo antes de finalizar o projeto.
    Após a finalização, o projeto ficará bloqueado para edição.
</p>

{{-- ================= PROJETO ================= --}}
<div class="mb-4">
    <h6 class="fw-semibold text-uppercase text-secondary">Projeto</h6>
    <ul class="list-group">
        <li class="list-group-item">
            <strong>Nome:</strong> {{ $project->name }}
        </li>
        <li class="list-group-item">
            <strong>Unidade:</strong> {{ $project->unit->name ?? '-' }}
        </li>
        <li class="list-group-item">
            <strong>Instituição:</strong> {{ $project->institution->name ?? '-' }}
        </li>
        <li class="list-group-item">
            <strong>Período:</strong>
            {{ \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') }}
            —
            {{ $project->end_date
                ? \Carbon\Carbon::parse($project->end_date)->format('d/m/Y')
                : 'Indeterminado'
            }}
        </li>
    </ul>
</div>

{{-- ================= CARGOS ================= --}}
<div class="mb-4">
    <h6 class="fw-semibold text-uppercase text-secondary">Cargos</h6>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Cargo</th>
                    <th>Valor Hora (R$)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->positions as $position)
                    <tr>
                        <td>{{ $position->name }}</td>
                        <td>{{ number_format($position->pivot->hourly_rate, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ================= CURSOS ================= --}}
<div class="mb-4">
    <h6 class="fw-semibold text-uppercase text-secondary">Cursos</h6>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Curso</th>
                    <th>Semestre</th>
                    <th>Ano</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->courses as $course)
                    <tr>
                        <td>{{ $course->name }}</td>
                        <td>{{ $course->pivot->semester ?? '-' }}</td>
                        <td>{{ $course->pivot->year ?? '-' }}</td>
                        <td>
                            @if($course->pivot->active)
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

{{-- ================= BOLSISTAS ================= --}}
<div class="mb-4">
    <h6 class="fw-semibold text-uppercase text-secondary">Bolsistas</h6>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Bolsista</th>
                    <th>Cargo</th>
                    <th>Carga Horária</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->scholarshipHolders as $holder)
                    <tr>
                        <td>{{ $holder->user->name }}</td>
                        <td>{{ $holder->pivot->position->name ?? '-' }}</td>
                        <td>{{ $holder->pivot->weekly_workload ?? '-' }} h</td>
                        <td>
                            <span class="badge
                                {{ $holder->pivot->status === 'active'
                                    ? 'bg-success'
                                    : 'bg-secondary'
                                }}">
                                {{ ucfirst($holder->pivot->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ================= FOMENTO ================= --}}
<div class="mb-4">
    <h6 class="fw-semibold text-uppercase text-secondary">Fontes de Fomento</h6>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Fonte</th>
                    <th>Valor (R$)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->fundingSources as $source)
                    <tr>
                        <td>{{ $source->name }}</td>
                        <td>{{ number_format($source->pivot->amount, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ================= AÇÕES ================= --}}
<div class="d-flex justify-content-between mt-4">
    <a href="{{ route('admin.projects.create.step5', $project) }}"
       class="btn btn-outline-secondary">
        ← Voltar
    </a>

    <form method="POST"
          action="{{ route('admin.projects.finalize', $project) }}">
        @csrf
        <button type="submit"
                class="btn btn-success"
                onclick="return confirm('Tem certeza que deseja finalizar este projeto?')">
            Finalizar Projeto ✔
        </button>
    </form>
</div>

@endsection

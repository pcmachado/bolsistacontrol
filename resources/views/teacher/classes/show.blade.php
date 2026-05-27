@extends('layouts.app')

@section('title', 'Turma')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-hero mb-4">
        <div class="d-flex justify-content-between flex-wrap gap-3">

            <div>
                <h3 class="fw-bold mb-1">👨‍🏫 {{ $offering->name }}</h3>
                <div class="text-muted">
                    {{ $offering->course->name }} •
                    {{ $offering->start_date }} → {{ $offering->end_date }}
                </div>
            </div>

            <div class="text-end">
                <div class="small text-muted">Disciplina</div>
                <strong>{{ $selectedDiscipline?->name ?? 'Selecione' }}</strong>
            </div>

        </div>
    </div>

    {{-- PROGRESS --}}
    <div class="card mb-4">
        <div class="card-body">

            <div class="row g-4">

                {{-- CURSO --}}
                <div class="col-md-6">
                    <div class="small text-muted mb-1">Progresso do curso</div>

                    <div class="d-flex justify-content-between">
                        <strong>{{ $offering->course->name }}</strong>
                        <span>{{ $progress }}%</span>
                    </div>

                    <div class="progress mt-1">
                        <div class="progress-bar bg-primary"
                            style="width: {{ $progress }}%">
                        </div>
                    </div>
                </div>

                {{-- DISCIPLINA --}}
                <div class="col-md-6">
                    <div class="small text-muted mb-1">Progresso da disciplina</div>

                    <div class="d-flex justify-content-between">
                        <strong>{{ $selectedDiscipline?->name ?? 'Selecione' }}</strong>
                        <span>
                            {{ $selectedDiscipline
                                ? ($disciplineProgress[$selectedDiscipline->id] ?? 0)
                                : 0 }}%
                        </span>
                    </div>

                    <div class="progress mt-1">
                        <div class="progress-bar bg-success"
                            style="width: {{ $selectedDiscipline
                                ? ($disciplineProgress[$selectedDiscipline->id] ?? 0)
                                : 0 }}%">
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- FILTROS --}}
    <div class="card mb-4">
        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Disciplina</label>
                    <select class="form-select"
                        onchange="location.href=this.value">

                        <option>Selecione</option>

                        @foreach($disciplines as $d)
                            <option
                                value="{{ route('teacher.classes.show', [$offering, 'discipline_id'=>$d->id]) }}"
                                @selected($selectedDiscipline?->id === $d->id)>
                                {{ $d->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Buscar aluno</label>
                    <form method="GET">
                        <input type="hidden" name="discipline_id" value="{{ $selectedDiscipline?->id }}">
                        <input type="search" name="student_name"
                               value="{{ $studentName }}"
                               class="form-control"
                               placeholder="Digite o nome do aluno">
                    </form>
                </div>

            </div>

        </div>
    </div>

    {{-- ALERT --}}
    @if(!$selectedDiscipline)
        <div class="alert alert-warning">
            Selecione uma disciplina para lançar frequência.
        </div>
    @endif

    {{-- RESUMO --}}
    <div class="dashboard-grid mb-4">

        <x-dashboard.summary-card
            title="Progresso Geral"
            :value="$progress.'%'"
            icon="bi-graph-up"
        />

        <x-dashboard.summary-card
            title="Alunos"
            :value="$students->count()"
            icon="bi-people"
        />

        <x-dashboard.summary-card
            title="Meses"
            :value="count($months)"
            icon="bi-calendar"
        />

    </div>

    {{-- LEGENDA --}}
    <div class="alert alert-light border mb-3">

        <strong>Como preencher:</strong>

        <div class="mt-2 small">

            <span class="badge bg-danger me-2">F</span>
            Faltas não justificadas (impactam pagamento)

            <br>

            <span class="badge bg-warning text-dark me-2">J</span>
            Faltas justificadas (não impactam pagamento)

            <br>

            <span class="badge bg-primary me-2">h</span>
            Carga horária automática baseada nas aulas

            <br>

            ⚠ Linhas destacadas indicam faltas acima de 15% da carga mensal

        </div>

    </div>

    {{-- TABELA --}}
    <form method="POST" action="{{ route('teacher.classes.monthly.save', $offering) }}">
        @csrf

        <div class="card shadow-sm">
            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 220px;">Aluno</th>

                            @foreach($months as $month)
                                <th class="text-center">

                                    <div class="fw-bold">
                                        {{ \Carbon\Carbon::parse($month)->format('m/Y') }}
                                    </div>

                                    <div class="small text-muted">
                                        {{ number_format($monthlyLoads[$month] ?? 0,1) }}h
                                    </div>

                                    <div>
                                        @php $s = $submissions[$month] ?? null; @endphp

                                        @if($s?->status === 'approved')
                                            <span class="badge bg-success">Fechado</span>
                                        @elseif($s?->status === 'submitted')
                                            <span class="badge bg-warning text-dark">Enviado</span>
                                        @else
                                            <span class="badge bg-secondary">Aberto</span>
                                        @endif
                                    </div>

                                </th>
                            @endforeach

                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($students as $student)

                            @php 
                                $total = 0;
                                $totalJ = 0;
                                $totalAbsences = 0;
                                $totalLoad = 0;

                                foreach ($months as $month) {
                                    $r = $monthRecords[$student->id][$month] ?? null;

                                    $abs = $r->absences ?? 0;
                                    $load = $monthlyLoads[$month] ?? 0;

                                    $totalAbsences += $abs;
                                    $totalLoad += $load;
                                }

                                $percentAbsence = $totalLoad > 0
                                    ? ($totalAbsences / $totalLoad) * 100
                                    : 0;

                                $isHighAbsence = $percentAbsence > 15;
                            @endphp

                            <tr class="{{ $percentAbsence > 15 ? 'table-danger' : '' }}">

                                {{-- NOME --}}
                                <td>
                                    <strong>{{ $student->user->name ?? $student->name }}</strong>

                                    @if($isHighAbsence)
                                        <div class="small text-danger fw-bold">
                                            ⚠ {{ number_format($percentAbsence,1) }}%
                                        </div>
                                    @endif
                                </td>

                                @foreach($months as $month)

                                    @php
                                        $r = $monthRecords[$student->id][$month] ?? null;
                                        $abs = $r->absences ?? 0;
                                        $jus = $r->justified_absences ?? 0;

                                        $total += $abs;
                                        $totalJ += $jus;

                                        $status = $submissions[$month]->status ?? null;
                                        $locked = in_array($status, ['submitted', 'approved']);

                                        $load = $monthlyLoads[$month] ?? 0;

                                        $percentAbsence = $load > 0
                                            ? ($abs / $load) * 100
                                            : 0;

                                        $isHighAbsence = $percentAbsence > 15;
                                    @endphp

                                    <td class="text-center {{ $isHighAbsence ? 'bg-danger-subtle' : '' }}">

                                        {{-- HORAS --}}
                                        <div class="small text-primary fw-bold">
                                            {{ number_format($monthlyLoads[$month] ?? 0,1) }}h
                                        </div>

                                        {{-- INPUTS --}}
                                        <div class="d-flex flex-column align-items-center gap-1 mt-1">

                                            {{-- faltas --}}
                                            <input type="number"
                                                name="records[{{ $student->id }}][{{ $month }}][absences]"
                                                value="{{ $abs }}"
                                                class="form-control form-control-sm text-center border-danger"
                                                style="width:65px"
                                                placeholder="Faltas"
                                                title="{{ $percentAbsence > 15 ? 'Atenção: faltas acima de 15%' : 'Faltas não justificadas' }}"
                                                {{ $locked ? 'readonly' : '' }}>

                                            {{-- justificadas --}}
                                            <input type="number"
                                                name="records[{{ $student->id }}][{{ $month }}][justified]"
                                                value="{{ $jus }}"
                                                class="form-control form-control-sm text-center border-warning"
                                                style="width:65px"
                                                placeholder="Just."
                                                title="Faltas justificadas"
                                                {{ $locked ? 'readonly' : '' }}>

                                        </div>

                                    </td>

                                @endforeach

                                <td class="fw-bold text-center">
                                    {{ $total }} / {{ $totalJ }}
                                </td>

                                <td>
                                    @php $rec = $studentRecords[$student->id] ?? null; @endphp

                                    @if($rec)
                                        <span class="badge bg-{{ $rec->status === 'approved' ? 'success':'danger' }}">
                                            {{ $rec->status }}
                                        </span>
                                    @endif
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>
        </div>

        {{-- AÇÕES --}}
        <div class="d-flex justify-content-between mt-3">

            <button class="btn btn-primary">
                💾 Salvar
            </button>

        </div>

    </form>

</div>
@endsection
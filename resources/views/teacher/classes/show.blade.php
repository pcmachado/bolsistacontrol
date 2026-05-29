@extends('layouts.app')

@section('title', 'Diário da Turma')

@section('content')

<div class="container-fluid">

    {{-- HERO --}}
    <div class="dashboard-hero mb-4">

        <div class="d-flex justify-content-between flex-wrap gap-3">

            <div>

                <h3 class="fw-bold mb-1">
                    👨‍🏫 {{ $offering->name }}
                </h3>

                <div class="text-muted">

                    {{ $offering->course->name }}

                    •

                    {{ \Carbon\Carbon::parse($offering->start_date)->format('d/m/Y') }}

                    →

                    {{ $offering->end_date
                        ? \Carbon\Carbon::parse($offering->end_date)->format('d/m/Y')
                        : 'Atual'
                    }}

                </div>

            </div>

            <div class="text-end">

                <div class="small text-muted">
                    Disciplina
                </div>

                <strong>
                    {{ $selectedDiscipline?->name ?? 'Selecione' }}
                </strong>

            </div>

        </div>

    </div>

    {{-- FILTROS --}}
    <div class="card mb-4 shadow-sm">

        <div class="card-body">

            <div class="row g-3">

                {{-- DISCIPLINA --}}
                <div class="col-md-6">

                    <label class="form-label">
                        Disciplina
                    </label>

                    <select
                        class="form-select"

                        onchange="window.location=this.value"
                    >

                        @foreach($disciplines as $discipline)

                            <option
                                value="{{ route(
                                    'teacher.classes.show',
                                    [
                                        $offering,
                                        'discipline_id' => $discipline->id,
                                        'month' => $monthKey,
                                    ]
                                ) }}"

                                @selected(
                                    $selectedDiscipline?->id === $discipline->id
                                )
                            >

                                {{ $discipline->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                {{-- BUSCA --}}
                <div class="col-md-6">

                    <label class="form-label">
                        Buscar aluno
                    </label>

                    <form method="GET">

                        <input
                            type="hidden"
                            name="discipline_id"
                            value="{{ $selectedDiscipline?->id }}"
                        >

                        <input
                            type="hidden"
                            name="month"
                            value="{{ $monthKey }}"
                        >

                        <input
                            type="search"
                            name="student_name"

                            value="{{ $studentName }}"

                            class="form-control"

                            placeholder="Digite o nome do aluno..."
                        >

                    </form>

                </div>

            </div>

        </div>

    </div>

    {{-- ALERTA --}}
    @if(!$selectedDiscipline)

        <div class="alert alert-warning">

            Selecione uma disciplina para continuar.

        </div>

    @else

        {{-- NAVEGAÇÃO MENSAL --}}
        <div class="card mb-4 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        @if($canGoPrev)

                            <a
                                href="{{ route(
                                    'teacher.classes.show',
                                    [
                                        $offering,
                                        'discipline_id' => $selectedDiscipline->id,
                                        'month' => $prevMonth->format('Y-m'),
                                    ]
                                ) }}"

                                class="btn btn-outline-secondary"
                            >

                                ← {{ $prevMonth->translatedFormat('m/Y') }}

                            </a>

                        @endif

                    </div>

                    <div class="text-center">

                        <h4 class="fw-bold mb-1">

                            {{ $currentMonth->translatedFormat('F/Y') }}

                        </h4>

                        <div class="small text-muted">

                            {{ number_format($monthlyLoad,1) }}h previstas
                            â€¢
                            {{ $monthlyClassDays }} dias/aulas

                        </div>

                    </div>

                    <div>

                        @if($canGoNext)

                            <a
                                href="{{ route(
                                    'teacher.classes.show',
                                    [
                                        $offering,
                                        'discipline_id' => $selectedDiscipline->id,
                                        'month' => $nextMonth->format('Y-m'),
                                    ]
                                ) }}"

                                class="btn btn-outline-secondary"
                            >

                                {{ $nextMonth->translatedFormat('m/Y') }} →

                            </a>

                        @endif

                    </div>

                </div>

            </div>

        </div>

        {{-- KPIs --}}
        <div class="dashboard-grid mb-4">

            <x-dashboard.summary-card
                title="Alunos"
                :value="$students->count()"
                icon="bi-people"
            />

            <x-dashboard.summary-card
                title="Carga Horária"
                :value="number_format($monthlyLoad,1).'h'"
                icon="bi-clock"
            />

            <x-dashboard.summary-card
                title="Dias/Aulas"
                :value="$monthlyClassDays.' / '.$plannedClassDays"
                icon="bi-calendar-check"
            />

            <x-dashboard.summary-card
                title="Horas por Dia"
                :value="number_format($hoursPerDay,2,',','.').'h'"
                icon="bi-hourglass-split"
            />

            <x-dashboard.summary-card
                title="Situação"
                :value="$submission?->status ?? 'aberto'"
                icon="bi-check-circle"
            />

        </div>

        {{-- LEGENDA --}}
        <div class="alert alert-light border mb-4">

            <strong>
                Como preencher:
            </strong>

            <div class="small mt-2">

                <span class="badge bg-danger me-2">
                    F
                </span>

                Faltas não justificadas

                <br>

                <span class="badge bg-warning text-dark me-2">
                    J
                </span>

                Faltas justificadas

                <br>

                ⚠ Alunos destacados excederam 15% de faltas.

            </div>

        </div>

        {{-- FORM --}}
        <form
            method="POST"

            action="{{ route(
                'teacher.classes.monthly.save',
                $offering
            ) }}"
        >

            @csrf

            <input
                type="hidden"
                name="discipline_id"
                value="{{ $selectedDiscipline->id }}"
            >

            <input
                type="hidden"
                name="month"
                value="{{ $monthKey }}"
            >

            {{-- TABELA --}}
            <div class="card shadow-sm">

                <div class="table-responsive">

                    <table class="table align-middle table-hover mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>
                                    Aluno
                                </th>

                                <th width="140">
                                    Faltas
                                </th>

                                <th width="140">
                                    Justificadas
                                </th>

                                <th width="120">
                                    %
                                </th>

                                <th width="160">
                                    Situação
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($students as $student)

                                @php

                                    $record =
                                        $records[$student->id]
                                        ?? null;

                                    $absences =
                                        $record->absences ?? 0;

                                    $justified =
                                        $record->justified_absences ?? 0;

                                    $percent =
                                        $monthlyClassDays > 0
                                        ? ($absences / $monthlyClassDays) * 100
                                        : 0;

                                    $absenceHours =
                                        $absences * $hoursPerDay;

                                    $isHighAbsence =
                                        $percent > 15;

                                    $locked = in_array(
                                        $submission?->status,
                                        ['submitted', 'approved']
                                    );

                                @endphp

                                <tr class="{{ $isHighAbsence ? 'table-danger' : '' }}">

                                    {{-- ALUNO --}}
                                    <td>

                                        <strong>

                                            {{ $student->user->name
                                                ?? $student->name
                                            }}

                                        </strong>

                                        @if($isHighAbsence)

                                            <div class="small text-danger fw-bold">

                                                ⚠ {{ number_format($percent,1) }}%

                                            </div>

                                        @endif

                                    </td>

                                    {{-- FALTAS --}}
                                    <td>

                                        <input
                                            type="number"

                                            min="0"

                                            name="records[{{ $student->id }}][{{ $monthKey }}][absences]"

                                            value="{{ $absences }}"

                                            class="form-control border-danger text-center"

                                            {{ $locked ? 'readonly' : '' }}
                                        >

                                    </td>

                                    {{-- JUSTIFICADAS --}}
                                    <td>

                                        <input
                                            type="number"

                                            min="0"

                                            name="records[{{ $student->id }}][{{ $monthKey }}][justified]"

                                            value="{{ $justified }}"

                                            class="form-control border-warning text-center"

                                            {{ $locked ? 'readonly' : '' }}
                                        >

                                    </td>

                                    {{-- % --}}
                                    <td>

                                        <span class="fw-bold">

                                            {{ number_format($percent,1) }}%

                                        </span>

                                        <div class="small text-muted">
                                            {{ number_format($absenceHours,1,',','.') }}h
                                        </div>

                                        <input
                                            type="hidden"
                                            name="records[{{ $student->id }}][{{ $monthKey }}][total]"
                                            value="{{ $monthlyClassDays }}"
                                        >

                                    </td>

                                    {{-- STATUS --}}
                                    <td>

                                        @if($submission?->status === 'approved')

                                            <span class="badge bg-success">

                                                Fechado

                                            </span>

                                        @elseif($submission?->status === 'submitted')

                                            <span class="badge bg-warning text-dark">

                                                Enviado

                                            </span>

                                        @else

                                            <span class="badge bg-secondary">

                                                Aberto

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
            <div class="d-flex justify-content-between mt-4">

                <button class="btn btn-primary">

                    💾 Salvar Frequência

                </button>

                @if(! in_array(
                    $submission?->status,
                    ['submitted', 'approved']
                ))

                    <button
                        type="submit"
                        formaction="{{ route(
                            'teacher.classes.monthly.close',
                            [$offering]
                        ) }}"

                        class="btn btn-success"
                    >

                        ✅ Fechar Mês

                    </button>

                @endif

            </div>

        </form>

    @endif

</div>

@endsection

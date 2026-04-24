@extends('layouts.app')

@section('title', 'Turma')

@section('content')
<div class="container-fluid">

    <h4 class="mb-4 fw-bold">
        Turma: {{ $offering->name }}
    </h4>

    {{-- HEADER --}}
    <div class="card mb-4">
        <div class="card-body">

            <div class="row">
                <div class="col-md-4">
                    <strong>Período:</strong><br>
                    {{ $offering->start_date }} até {{ $offering->end_date }}
                </div>

                <div class="col-md-4">
                    <strong>Curso:</strong><br>
                    {{ $offering->course->name }}
                </div>

                <div class="col-md-4">
                    <strong>Disciplina:</strong><br>
                    {{ $offering->disciplines->first()?->name }}
                </div>

                <div class="col-md-4">
                    <strong>Progresso:</strong>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-success"
                             style="width: {{ $progress }}%">
                            {{ $progress }}%
                        </div>
                    </div>
                </div>

                @foreach($disciplines as $discipline)

                    <div class="mb-2">
                        <strong>{{ $discipline->name }}</strong>

                        <div class="progress">
                            <div class="progress-bar bg-success"
                                style="width: {{ $disciplineProgress[$discipline->id] ?? 0 }}%">
                                {{ $disciplineProgress[$discipline->id] ?? 0 }}%
                            </div>
                        </div>
                    </div>

                @endforeach
            </div>

        </div>
    </div>

    {{-- FORM --}}
    <form method="POST" action="{{ route('teacher.classes.monthly.save', $offering) }}">
        @csrf

        <div class="card">
            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered align-middle">

                        <thead>
                            <tr>
                                <th>Aluno</th>

                                @foreach($months as $month)
                                    <th class="text-center">
                                        {{ \Carbon\Carbon::parse($month)->format('m/Y') }}
                                        <br>

                                        @php
                                            $submission = $submissions[$month] ?? null;
                                        @endphp

                                        @if($submission && $submission->status === 'approved')
                                            🔒
                                        @elseif($submission && $submission->status === 'submitted')
                                            ⏳
                                        @else
                                            🔓
                                        @endif
                                    </th>
                                @endforeach

                                <th>Total Faltas</th>
                                <th>Situação</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($students as $student)
                                <tr>

                                    <td>
                                        {{ $student->user->name ?? $student->name }}
                                    </td>

                                    @php $total = 0; @endphp

                                    @foreach($months as $month)

                                        @php
                                            $record = $monthRecords[$student->id][$month] ?? null;
                                            $value = $record->absences ?? 0;
                                            $total += $value;

                                            $submission = $submissions[$month] ?? null;
                                            $locked = $submission && $submission->status !== 'draft';
                                        @endphp

                                        <td>
                                            <input type="number"
                                                   name="records[{{ $student->id }}][{{ $month }}]"
                                                   value="{{ $value }}"
                                                   class="form-control text-center"
                                                   min="0"
                                                   {{ $locked ? 'readonly' : '' }}>
                                        </td>

                                    @endforeach

                                    <td class="text-center fw-bold">
                                        {{ $total }}
                                    </td>

                                    <td>
                                        @php
                                            $record = $studentRecords[$student->id] ?? null;
                                        @endphp

                                        @if($record)
                                            <span class="badge bg-{{ $record->status === 'approved' ? 'success' : 'danger' }}">
                                                {{ $record->status === 'approved' ? 'Aprovado' : 'Reprovado' }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>
        </div>

        <div class="mt-3 text-end">
            <button class="btn btn-primary">
                💾 Salvar Frequência
            </button>
        </div>

    </form>

    {{-- FECHAMENTO --}}
    <div class="card mt-4">
        <div class="card-body">

            <h5>Fechamento Mensal</h5>

            <div class="d-flex gap-2 flex-wrap">

                @foreach($months as $month)

                    @php
                        $submission = $submissions[$month] ?? null;
                    @endphp

                    <form method="POST"
                          action="{{ route('teacher.classes.monthly.close', [$offering, $month]) }}">
                        @csrf

                        <button class="btn btn-sm
                            {{ $submission ? 'btn-secondary' : 'btn-success' }}"
                            {{ $submission ? 'disabled' : '' }}>

                            {{ \Carbon\Carbon::parse($month)->format('m/Y') }}

                            @if($submission)
                                ✔
                            @endif
                        </button>

                    </form>

                @endforeach

            </div>

        </div>
    </div>

</div>
@endsection
@extends('layouts.app')

@section('title', 'Lançamento da Turma')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>
            🧾 Lançamento — {{ $class->name ?? 'Turma '.$class->id }}
        </h3>

        <a href="{{ route('admin.class.students.list', $class) }}"
           class="btn btn-outline-secondary">
            ← Voltar
        </a>
    </div>

     @if($availableMonths->isEmpty())
        <div class="alert alert-info">
            Nenhum lançamento encontrado para esta turma.
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">

        {{-- anterior --}}
        @if($canGoPrev)
            <a href="{{ request()->fullUrlWithQuery([
                'month' => $prev->month,
                'year'  => $prev->year
            ]) }}"
            class="btn btn-outline-secondary btn-sm">
                ← {{ $prev->format('m/Y') }}
            </a>
        @else
            <button class="btn btn-outline-secondary btn-sm" disabled>
                ←
            </button>
        @endif

        {{-- atual --}}
        <strong class="fs-5">
            {{ \Carbon\Carbon::create($year, $month)->format('F/Y') }}
        </strong>

        {{-- próximo --}}
        @if($canGoNext)
            <a href="{{ request()->fullUrlWithQuery([
                'month' => $next->month,
                'year'  => $next->year
            ]) }}"
            class="btn btn-outline-secondary btn-sm">
                {{ $next->format('m/Y') }} →
            </a>
        @else
            <button class="btn btn-outline-secondary btn-sm" disabled>
                →
            </button>
        @endif

    </div>

    <div class="mb-3 d-flex flex-wrap gap-2">

    @foreach($monthsData as $m)

        @php
            $isCurrent = $month == $m['month'] && $year == $m['year'];

            $color = match($m['status']) {
                'draft' => 'secondary',
                'submitted' => 'warning',
                'approved' => 'success',
                'rejected' => 'danger',
                default => 'secondary'
            };
        @endphp

        <a href="{{ request()->fullUrlWithQuery([
                'month' => $m['month'],
                'year'  => $m['year']
            ]) }}"
        class="btn btn-sm
                {{ $isCurrent ? "btn-$color" : "btn-outline-$color" }}">

            {{ sprintf('%02d/%s', $m['month'], $m['year']) }}

            {{-- 🔒 bloqueado --}}
            @if(!$m['canSubmit'] && $m['status'] === 'draft')
                🔒
            @endif

        </a>

    @endforeach

    </div>

    <form method="POST">
        @csrf

        <div class="card shadow-sm">
            <div class="card-body p-0">

                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Aluno</th>
                            <th width="120">Aulas</th>
                            <th width="120">Faltas</th>
                            <th width="120">Presenças</th>
                            <th width="150">Situação</th>
                            <th width="150">Valor</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($students as $student)

                            @php
                                $r = $records[$student->id] ?? null;
                                $locked = !$submission || $submission?->status !== 'submitted' || $submission->status !== 'approved';
                            @endphp

                            <tr class="student-row">

                                {{-- Nome --}}
                                <td>
                                    <strong>{{ $student->name }}</strong>
                                </td>

                                {{-- Total aulas --}}
                                <td>
                                    <input type="number"
                                        name="students[{{ $student->id }}][total_classes]"
                                        value="{{ $r->total_classes ?? 20 }}"
                                        class="form-control form-control-sm total" {{ $locked ? 'disabled' : '' }}>
                                </td>

                                {{-- Faltas --}}
                                <td>
                                    <input type="number"
                                        name="students[{{ $student->id }}][absences]"
                                        value="{{ $r->absences ?? 0 }}"
                                        class="form-control form-control-sm absences" {{ $locked ? 'disabled' : '' }}>
                                </td>

                                {{-- Presenças --}}
                                <td class="attended fw-bold">
                                    {{ $r->attended_classes ?? 0 }}
                                </td>

                                {{-- Situação --}}
                                <td>
                                    <select name="students[{{ $student->id }}][status]"
                                            class="form-select form-select-sm" {{ $locked ? 'disabled' : '' }}>
                                        <option value="approved">Aprovado</option>
                                        <option value="failed">Reprovado</option>
                                        <option value="canceled">Cancelado</option>
                                    </select>
                                </td>

                                {{-- Valor --}}
                                <td class="total-value text-success fw-bold">
                                    R$ {{ number_format($total_amount ?? 0,2,',','.') }}
                                </td>

                            </tr>

                        @endforeach
                    </tbody>

                </table>

            </div>
        </div>

        {{-- TOTAL GERAL --}}
        <div class="card mt-3 shadow-sm">
            <div class="card-body d-flex justify-content-between">

                <strong>Total geral</strong>

                <span id="grandTotal" class="fw-bold text-success">
                    R$ 0,00
                </span>

            </div>
        </div>

    </form>

    <div class="d-flex justify-content-between mt-3">

        <button class="btn btn-success">
            💾 Salvar Lançamentos
        </button>

        @if($submission?->status === 'draft')

            @if($canSubmitCurrentMonth)
                <form method="POST" action="{{ route('admin.class.students.submit', $class) }}">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">

                    <button class="btn btn-primary">
                        Enviar mês
                    </button>
                </form>
            @else
                <button class="btn btn-secondary" disabled>
                    🔒 Envie o mês anterior primeiro
                </button>
            @endif

        @endif

    </div>

</div>
@endsection

@push('scripts')
<script>

const rate = {{ $rate ?? 0 }};

function calculateRow(row) {
    let total = parseInt(row.querySelector('.total').value) || 0;
    let absences = parseInt(row.querySelector('.absences').value) || 0;

    let attended = Math.max(0, total - absences);

    let value = attended * rate;

    row.querySelector('.attended').innerText = attended;
    row.querySelector('.total-value').innerText =
        'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 2});
}

function calculateAll() {
    let total = 0;

    document.querySelectorAll('.student-row').forEach(row => {
        calculateRow(row);

        let valueText = row.querySelector('.total-value').innerText
            .replace('R$', '')
            .replace('.', '')
            .replace(',', '.');

        total += parseFloat(valueText) || 0;
    });

    document.getElementById('grandTotal').innerText =
        'R$ ' + total.toLocaleString('pt-BR', {minimumFractionDigits: 2});
}

// eventos
document.querySelectorAll('.total, .absences').forEach(input => {
    input.addEventListener('input', calculateAll);
});

// inicial
calculateAll();

</script>
@endpush
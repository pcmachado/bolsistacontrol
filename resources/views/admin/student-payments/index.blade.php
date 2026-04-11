@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h3 class="mb-3">💰 Pagamentos de Alunos</h3>

    <div class="mb-3 d-flex gap-2">

        <a href="{{ route('admin.student-payments.report.pdf', request()->all()) }}"
        class="btn btn-danger">
            📄 PDF
        </a>

        <a href="{{ route('admin.student-payments.report.excel', request()->all()) }}"
        class="btn btn-success">
            📊 Excel
        </a>

    </div>

    {{-- 🔎 filtros --}}
    <form method="GET" class="row g-2 mb-3">

        <div class="col-md-1">
            <input type="number" name="month" class="form-control"
                value="{{ request('month') }}" placeholder="Mês">
        </div>

        <div class="col-md-1">
            <input type="number" name="year" class="form-control"
                value="{{ request('year') }}" placeholder="Ano">
        </div>

        <div class="col-md-2">
            <select name="status" class="form-control">
                <option value="">Situação</option>
                <option value="pending">Pendente</option>
                <option value="sent">Enviado</option>
                <option value="paid">Pago</option>
            </select>
        </div>

        {{-- 🔥 UNIDADE --}}
        <div class="col-md-3">
            <select name="unit_id" class="form-control">
                <option value="">Unidade</option>
                @foreach(\App\Models\Unit::all() as $unit)
                    <option value="{{ $unit->id }}"
                        {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- 🔥 CURSO --}}
        <div class="col-md-3">
            <select name="course_id" class="form-control">
                <option value="">Curso</option>
                @foreach(\App\Models\Course::all() as $course)
                    <option value="{{ $course->id }}"
                        {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- TURMA --}}
        <div class="col-md-2">
            <select name="class_offering_id" class="form-control">
                <option value="">Turma</option>
                @foreach(\App\Models\ClassOffering::all() as $co)
                    <option value="{{ $co->id }}"
                        {{ request('class_offering_id') == $co->id ? 'selected' : '' }}>
                        {{ $co->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>

    </form>

    <div class="d-flex justify-content-between mb-3">

        <a href="{{ request()->fullUrlWithQuery([
            'month' => $prev->month,
            'year'  => $prev->year
        ]) }}" class="btn btn-outline-secondary btn-sm">
            ← {{ $prev->format('m/Y') }}
        </a>

        <strong>{{ $current->format('F/Y') }}</strong>

        <a href="{{ request()->fullUrlWithQuery([
            'month' => $next->month,
            'year'  => $next->year
        ]) }}" class="btn btn-outline-secondary btn-sm">
            {{ $next->format('m/Y') }} →
        </a>

    </div>

    <div class="mb-3 d-flex flex-wrap gap-2">

        @foreach($monthsData as $m)

            @php
                $isCurrent = $month == $m['month'];

                $color = match($m['status']) {
                    'paid' => 'success',
                    'pending' => 'warning',
                    'empty' => 'secondary',
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

                @if(!$m['hasData'])
                    •
                @endif

            </a>

        @endforeach

        </div>

    {{-- 💰 FORM DE LOTE --}}
    <form method="POST" action="{{ route('admin.student-payments.payBatch') }}">
        @csrf

        {!! $dataTable->table() !!}

        <div class="mt-3 d-flex justify-content-between">

            <div>
                <button class="btn btn-success">
                    💰 Pagar selecionados
                </button>
            </div>

        </div>

        <span id="selected-count" class="text-muted"></span>

    </form>

</div>
@endsection

@push('scripts')
{!! $dataTable->scripts() !!}

<script>
document.addEventListener('DOMContentLoaded', function () {

    const selectAll = document.getElementById('select-all');

    if (selectAll) {
        selectAll.addEventListener('click', function () {
            document.querySelectorAll('input[name="ids[]"]').forEach(cb => {
                cb.checked = this.checked;
            });
        });

        document.querySelectorAll('input[name="ids[]"]').forEach(cb => {
            cb.addEventListener('change', updateCount);
        });

        function updateCount() {
            const total = document.querySelectorAll('input[name="ids[]"]:checked').length;
            document.getElementById('selected-count').innerText =
                total + ' selecionados';
        }
    }

});
</script>
@endpush
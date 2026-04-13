@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h3 class="mb-3">📊 Dashboard Financeiro - Alunos</h3>

    {{-- filtros --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-2">
            <input type="number" name="month" class="form-control"
                value="{{ request('month') }}" placeholder="Mês">
        </div>

        <div class="col-md-2">
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

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    {{-- CARDS --}}
    <div class="row g-3">

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">Total do mês</small>
                    <h4>R$ {{ number_format($total,2,',','.') }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">Pago</small>
                    <h4 class="text-success">
                        R$ {{ number_format($paid,2,',','.') }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">Pendente</small>
                    <h4 class="text-danger">
                        R$ {{ number_format($pending,2,',','.') }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">Pagamentos</small>
                    <h4>{{ $count }}</h4>
                </div>
            </div>
        </div>

    </div>

    {{-- PROGRESSO --}}
    @php
        $percent = $total > 0 ? ($paid / $total) * 100 : 0;
    @endphp

    <div class="card mt-4">
        <div class="card-body">

            <div class="d-flex justify-content-between mb-2">
                <strong>Execução financeira</strong>
                <span>{{ number_format($percent,1) }}%</span>
            </div>

            <div class="progress" style="height: 10px;">
                <div class="progress-bar bg-success"
                     style="width: {{ $percent }}%"></div>
            </div>

        </div>
    </div>

    {{-- LISTAGEM DETALHADA --}}
    <div class="card mt-4">
        <div class="card-body">

            <h5 class="mb-3">Detalhamento dos pagamentos</h5>

            {!! $dataTable->table() !!}

        </div>
    </div>

</div>
@endsection

@push('scripts')
{!! $dataTable->scripts() !!}
@endpush
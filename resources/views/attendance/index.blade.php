@extends('layouts.app')

@section('title', 'Meus Registros de Frequência')

@section('content')
<div class="container">
    <h3 class="mb-4"><i class="bi bi-clock-history me-2"></i> Meus Registros de Frequência</h3>

    {{-- Botão para criar novo registro --}}
    <div class="mb-3">
        <a href="{{ route('attendance.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Novo Registro
        </a>
    </div>

    {{-- ========================
        FILTROS
    ========================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('attendance.my') }}" class="row g-3 align-items-end">

                {{-- Período --}}
                <div class="col-md-2">
                    <label for="start_date" class="form-label">Data Inicial</label>
                    <input type="date" name="start_date" id="start_date" 
                        value="{{ request('start_date') }}" class="form-control">
                </div>

                <div class="col-md-2">
                    <label for="end_date" class="form-label">Data Final</label>
                    <input type="date" name="end_date" id="end_date" 
                        value="{{ request('end_date') }}" class="form-control">
                </div>

                {{-- Mês --}}
                <div class="col-md-2">
                    <label for="monthYear" class="form-label">Mês</label>
                    <select name="monthYear" id="monthYear" class="form-select">
                        <option value="">-- Todos --</option>
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ request('monthYear') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Ano --}}
                <div class="col-md-2">
                    <label for="year" class="form-label">Ano</label>
                    <select name="year" id="year" class="form-select">
                        <option value="">-- Todos --</option>
                        @foreach(range(date('Y')-5, date('Y')) as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">-- Todos --</option>
                        <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Aprovado</option>
                        <option value="submitted" {{ request('status')=='submitted' ? 'selected' : '' }}>Submetido</option>
                        <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejeitado</option>
                        <option value="draft" {{ request('status')=='draft' ? 'selected' : '' }}>Rascunho</option>
                        <option value="late" {{ request('status')=='late' ? 'selected' : '' }}>Atrasado</option>
                    </select>
                </div>

                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('attendance.my') }}" class="btn btn-secondary">
                        Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- DataTable --}}
    <div class="card shadow-sm">
        <div class="card-body">
            {!! $dataTable->table(['class' => 'table table-striped table-bordered align-middle'], true) !!}
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush

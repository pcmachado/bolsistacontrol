@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Frequências para Homologação</h3>

    {{-- 🔎 Filtros --}}
    <form id="filters" class="row g-3 mb-4">
        @if(auth()->user()->hasRole(['admin','coordenador_geral', 'coordenador_adjunto_geral']))
            <div class="col-md-3">
                <label for="unit_id" class="form-label">Unidade</label>
                <select name="unit_id" id="unit_id" class="form-select">
                    <option value="">Todas</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="col-md-3">
            <label for="scholarship_holder_id" class="form-label">Bolsista</label>
            <select name="scholarship_holder_id" id="scholarship_holder_id" class="form-select">
                <option value="">Todos</option>
                @foreach($scholarshipHolders as $holder)
                    <option value="{{ $holder->id }}">{{ $holder->user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label for="month" class="form-label">Mês</label>
            <input type="month" name="month" id="month" class="form-control">
        </div>

        <div class="col-md-2">
            <label for="start_date" class="form-label">De</label>
            <input type="date" name="start_date" id="start_date" class="form-control">
        </div>
        <div class="col-md-2">
            <label for="end_date" class="form-label">Até</label>
            <input type="date" name="end_date" id="end_date" class="form-control">
        </div>

        <div class="col-md-12">
            <button type="button" id="applyFilters" class="btn btn-primary">Aplicar Filtros</button>
            <button type="button" id="resetFilters" class="btn btn-secondary">Limpar</button>
        </div>
    </form>

    {{-- ⚡ Ações em lote --}}
    @include('admin.homologations.partials.bulk_actions')

    {{-- 📊 Tabela --}}
    <table id="homologations-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>Data</th>
                <th>Bolsista</th>
                <th>Unidade</th>
                <th>Duração</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
    </table>
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

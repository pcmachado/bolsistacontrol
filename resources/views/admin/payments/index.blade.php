@extends('layouts.app')

@section('title', 'Pagamentos')

@section('content')
<div class="container-fluid">

    <h1 class="mb-4">Pagamentos</h1>

    {{-- ============================= --}}
    {{-- FILTROS --}}
    {{-- ============================= --}}
    <form method="GET" class="row g-2 mb-3 align-items-end">

        <div class="col-md-2">
            <label class="form-label">Competência</label>
            <input 
                type="month" 
                name="month" 
                value="{{ request('month', now()->format('Y-m')) }}" 
                class="form-control">
        </div>

        <div class="col-md-2">
            <label class="form-label">Unidade</label>
            <select name="unit_id" class="form-select">
                <option value="">Todas</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" @selected(request('unit_id') == $unit->id)>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Projeto</label>
            <select name="project_id" class="form-select">
                <option value="">Todos</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" @selected(request('project_id') == $project->id)>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Cargo</label>
            <select name="position_id" class="form-select">
                <option value="">Todos</option>
                @foreach($positions as $position)
                    <option value="{{ $position->id }}" @selected(request('position_id') == $position->id)>
                        {{ $position->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Todos</option>
                @foreach([
                    'sent_to_payment' => 'Enviados',
                    'paid' => 'Pagos',
                    'confirmed' => 'Confirmados'
                ] as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.payments.reports.monthly', array_merge(request()->all(), ['pdf' => 1])) }}" target="_blank" class="btn btn-danger w-100">📄 PDF </a>
        </div>

    </form>

    {{-- ============================= --}}
    {{-- CARDS --}}
    {{-- ============================= --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light shadow-sm">
                <div class="card-body text-center">
                    <h6>Total Geral</h6>
                    <h4 id="total-geral">R$ {{ number_format($totalGeral ?? 0, 2, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>
    @if(isset($grouped))
    <div class="row mb-4">

        @foreach($grouped as $item)
        <div class="col-md-3">
            <div class="card shadow-sm border">
                <div class="card-body text-center">
                    <h6>{{ $item['unit'] }}</h6>
                    <small>{{ $item['count'] }} pagamentos</small>
                    <h5 class="mt-2">
                        R$ {{ number_format($item['total'], 2, ',', '.') }}
                    </h5>
                </div>
            </div>
        </div>
        @endforeach

    </div>
    @endif

    {{-- ============================= --}}
    {{-- DATATABLE --}}
    {{-- ============================= --}}
    <div class="card shadow-sm">
        <div class="card-body">
            {!! $dataTable->table() !!}
        </div>
    </div>

</div>
@endsection

@push('scripts')
{!! $dataTable->scripts() !!}

<script>
document.addEventListener("DOMContentLoaded", function () {

    const table = $('#payments-report-table').DataTable();

    function atualizarTotal() {

        let total = 0;

        table.rows({ search: 'applied' }).every(function () {
            let data = this.data();

            if (data.valor) {
                let valor = data.valor
                    .replace('.', '')
                    .replace(',', '.');

                total += parseFloat(valor);
            }
        });

        document.getElementById('total-geral')
            .innerText = 'R$ ' + total.toLocaleString('pt-BR', {
                minimumFractionDigits: 2
            });
    }

    table.on('draw', function () {
        atualizarTotal();
    });

});
</script>
@endpush

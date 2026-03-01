@extends('layouts.app')

@section('title', 'Pagamentos')

@section('content')
<div class="container-fluid">

    <h1 class="mb-4">Pagamentos</h1>

    {{-- ============================= --}}
    {{-- FILTROS --}}
    {{-- ============================= --}}
    <form method="GET" class="row g-2 mb-3 align-items-end">

        <div class="col-md-3">
            <label class="form-label">Mês</label>
            <input type="month"
                   name="month"
                   value="{{ request('month') }}"
                   class="form-control">
        </div>

        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">Todos</option>
                @foreach([
                    'sent_to_payment' => 'Enviados',
                    'paid' => 'Pagos',
                    'confirmed' => 'Confirmados'
                ] as $key => $label)
                    <option value="{{ $key }}"
                        @selected(request('status') === $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">
                Filtrar
            </button>
        </div>

    </form>

    {{-- ============================= --}}
    {{-- CARD TOTAL --}}
    {{-- ============================= --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light shadow-sm">
                <div class="card-body text-center">
                    <h6>Total Geral</h6>
                    <h4 id="total-geral">R$ 0,00</h4>
                </div>
            </div>
        </div>
    </div>

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

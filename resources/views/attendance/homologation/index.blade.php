@extends('layouts.app')

@section('title', 'Homologação de Frequências')

@section('content')
<h3>Registros Pendentes de Homologação</h3>

{{-- Filtros --}}
<form method="GET" id="filter-form" class="row g-3 mb-3">
    @if(auth()->user()->hasRole('Coordenador Geral'))
        <div class="col-md-4">
            <label for="unit_id" class="form-label">Unidade</label>
            <select name="unit_id" id="unit_id" class="form-select">
                <option value="">Todas</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>
    @else
        <div class="col-md-4">
            <label class="form-label">Unidade</label>
            <input type="text" class="form-control"
                   value="{{ auth()->user()->unit?->name ?? 'Sem unidade' }}" disabled>
        </div>
    @endif

    <div class="col-md-2 align-self-end">
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </div>
</form>

{{-- Botões de ação em lote --}}
<div class="mb-3">
    <button id="approve-selected" class="btn btn-success">
        <i class="bi bi-check-circle"></i> Aprovar Selecionados
    </button>
    <button id="reject-selected" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#bulkRejectModal">
        <i class="bi bi-x-circle"></i> Rejeitar Selecionados
    </button>
</div>

{{-- DataTable --}}
<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-striped table-bordered" id="homologation-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>Bolsista</th>
                    <th>Unidade</th>
                    <th>Data</th>
                    <th>Horas</th>
                    <th>Observação</th>
                    <th>Ações</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal de Recusa em Lote -->
<div class="modal fade" id="bulkRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="bulk-reject-form" method="POST" action="{{ route('admin.homologations.bulk') }}">
            @csrf
            <input type="hidden" name="action" value="reject">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Recusar Registros Selecionados</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="reason" class="form-control" placeholder="Informe o motivo da recusa" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Confirmar Recusa</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    let table = $('#homologation-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.homologations.index") }}',
            data: function (d) {
                d.unit_id = $('#unit_id').val();
            }
        },
        columns: [
            { data: 'checkbox', orderable: false, searchable: false },
            { data: 'scholarship_holder.name', name: 'scholarshipHolder.name' },
            { data: 'scholarship_holder.unit.name', name: 'scholarshipHolder.unit.name' },
            { data: 'date', name: 'date' },
            { data: 'hours', name: 'hours' },
            { data: 'observation', name: 'observation' },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    // Selecionar todos
    $('#select-all').on('click', function(){
        $('input[name="records[]"]').prop('checked', this.checked);
    });

    // Aprovar em lote
    $('#approve-selected').on('click', function(e){
        e.preventDefault();
        let ids = $('input[name="records[]"]:checked').map(function(){ return this.value; }).get();
        if(ids.length === 0) return alert('Selecione pelo menos um registro.');
        $.post('{{ route("admin.homologations.bulk") }}', {
            _token: '{{ csrf_token() }}',
            action: 'approve',
            records: ids
        }).done(() => location.reload());
    });

    // Submeter recusa em lote
    $('#bulk-reject-form').on('submit', function(e){
        e.preventDefault();
        let ids = $('input[name="records[]"]:checked').map(function(){ return this.value; }).get();
        if(ids.length === 0) return alert('Selecione pelo menos um registro.');
        let form = $(this);
        $.post(form.attr('action'), form.serialize() + '&records[]=' + ids.join('&records[]='), function(){
            location.reload();
        });
    });
});
</script>
@endpush

@extends('layouts.app')

@section('content')
<div class="card shadow-lg rounded-3">
    <div class="card-body p-4">
        <h2 class="h5 card-title fw-semibold mb-4">Todos os Envios de Frequência</h2>
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="month" name="month" value="{{ request('month') }}" class="form-control">
            </div>
            <div class="col-md-4">
                <select name="unit_id" class="form-select">
                    <option value="">Todas as Unidades</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" @selected(request('unit_id') == $unit->id)>
                            {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Bolsista</th>
                        <th>Data</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                        <tr>
                            <td>{{ $submission->scholarshipHolder->user->name }}</td>
                            <td>{{ $submission->date->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge text-bg-info rounded-pill px-3 py-1">Enviado</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted">Nenhum envio encontrado</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white pt-3 pb-0 border-top">
            {!! $submissions->links('pagination::bootstrap-5') !!}
        </div>
    </div>
</div>
@endsection

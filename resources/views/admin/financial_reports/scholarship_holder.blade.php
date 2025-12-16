@extends('layouts.app')

@section('title', 'Relatório por Bolsista')

@section('content')
<div class="container-fluid">

    <h3 class="mb-4">
        <i class="bi bi-person-vcard"></i>
        Relatório Financeiro por Bolsista
    </h3>

    <form class="card p-3 mb-4 shadow-sm">
        <div class="row g-3">

            <div class="col-md-4">
                <label>Bolsista</label>
                <select name="scholarship_holder_id" class="form-select" required>
                    <option value="">Selecione...</option>
                    @foreach($holders as $h)
                        <option value="{{ $h->id }}"
                            @selected($selectedId == $h->id)>
                            {{ $h->name }} ({{ $h->user->email ?? '' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Ano</label>
                <input type="number" class="form-control" name="year" value="{{ $filters['year'] }}">
            </div>

            <div class="col-md-3">
                <label>Projeto</label>
                <select name="project_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" @selected($filters['project'] == $p->id)>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="sent_to_payment" @selected($filters['status']=='sent_to_payment')>Enviado</option>
                    <option value="paid" @selected($filters['status']=='paid')>Pago</option>
                    <option value="confirmed" @selected($filters['status']=='confirmed')>Confirmado</option>
                </select>
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filtrar</button>
            </div>

        </div>
    </form>

    @if($payments->count())
        
        {{-- Botões PDF / Excel --}}
        <div class="mb-3">
            <a href="{{ route('admin.financial-reports.scholarship-holder.pdf', request()->query()) }}"
               target="_blank" class="btn btn-danger btn-sm">
                <i class="bi bi-filetype-pdf"></i> PDF
            </a>

            <a href="{{ route('admin.financial-reports.scholarship-holder.excel', request()->query()) }}"
               class="btn btn-success btn-sm">
                <i class="bi bi-filetype-xlsx"></i> Excel
            </a>
        </div>

        {{-- TABELA --}}
        <div class="card shadow-sm">
            <div class="card-body">

                <h5>Total:
                    <strong>
                        R$ {{ number_format($payments->sum('amount'),2,',','.') }}
                    </strong>
                </h5>

                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Referência</th>
                            <th>Projeto</th>
                            <th>Unidade</th>
                            <th>Status</th>
                            <th>Valor</th>
                            <th>Recibo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $p)
                            <tr>
                                <td>{{ $p->month }}/{{ $p->year }}</td>
                                <td>{{ $p->project->name }}</td>
                                <td>{{ $p->unit->name }}</td>
                                <td>@include('admin.financial_reports.status_badge', ['p'=>$p])</td>
                                <td>R$ {{ number_format($p->amount,2,',','.') }}</td>

                                <td>
                                    @if($p->receipt_number)
                                        <a href="{{ route('payments.receipt', $p) }}" target="_blank">
                                            {{ $p->receipt_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>

    @endif

</div>
@endsection

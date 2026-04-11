@extends('pdf') {{-- ou o nome correto do seu layout --}}

@section('title', 'Relatório de Pagamentos - Alunos')

@section('header-extra')
    <h5>Relatório de Pagamentos de Alunos</h5>
@endsection

@section('content')

    {{-- FILTROS --}}
    <table class="no-break">
        <tr>
            <td><strong>Mês:</strong> {{ request('month') ?? 'Todos' }}</td>
            <td><strong>Ano:</strong> {{ request('year') ?? 'Todos' }}</td>
            <td><strong>Status:</strong> {{ request('status') ?? 'Todos' }}</td>
        </tr>
        <tr>
            <td colspan="2">
                <strong>Unidade:</strong> {{ request('unit_id') ?? 'Todas' }}
            </td>
            <td>
                <strong>Curso:</strong> {{ request('course_id') ?? 'Todos' }}
            </td>
        </tr>
    </table>

    {{-- TABELA --}}
    <table>
        <thead>
            <tr>
                <th>Aluno</th>
                <th>Turma</th>
                <th>Unidade</th>
                <th>Curso</th>
                <th>Período</th>
                <th>Valor (R$)</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach($payments as $p)
                @php
                    $status = $p->computed_status ?? $p->status;
                @endphp
                <tr>
                    <td>{{ $p->student->name }}</td>
                    <td>{{ $p->classOffering->name }}</td>
                    <td>{{ $p->classOffering->unit->name }}</td>
                    <td>{{ $p->classOffering->course->name }}</td>
                    <td>{{ sprintf('%02d/%02d', $p->month, $p->year) }}</td>
                    <td style="text-align:right;">
                        {{ number_format($p->amount, 2, ',', '.') }}
                    </td>
                    <td>{{ ucfirst($status) }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="5">TOTAL</th>
                <th style="text-align:right;">
                    {{ number_format($total, 2, ',', '.') }}
                </th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    {{-- ASSINATURAS --}}
    <table class="assinaturas no-break">
        <tr>
            <td>
                <div class="assinatura-linha"></div>
                Coordenação Adjunta
            </td>

            <td>
                <div class="assinatura-linha"></div>
                Financeiro
            </td>
        </tr>
    </table>

@endsection
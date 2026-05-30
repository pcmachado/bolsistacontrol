<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Relatorio')</title>

    <style>
        @if($isPdf ?? false)
        @page {
            margin: 220px 40px 90px 40px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        header {
            position: fixed;
            top: -220px;
            left: 0;
            right: 0;
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
        }

        .no-break {
            page-break-inside: avoid;
        }

        .page-break {
            page-break-after: always;
        }
        @else
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            padding: 20px;
            line-height: 1.5;
        }

        header {
            position: relative;
            text-align: center;
            margin-bottom: 15px;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
        }
        @endif

        header h3 {
            margin: 2px 0;
            font-size: 13px;
            font-weight: bold;
        }

        header h4 {
            margin: 1px 0;
            font-size: 11px;
            font-weight: normal;
        }

        header h5 {
            margin: 1px 0;
            font-size: 10px;
            font-weight: normal;
        }

        h5 {
            margin-top: 20px;
        }

        .logos {
            width: 100%;
            margin-bottom: 5px;
        }

        .logos td {
            border: none;
            text-align: center;
            vertical-align: middle;
        }

        .logos img {
            max-height: 55px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        table.no-break {
            page-break-inside: avoid;
        }

        .assinaturas,
        .assinaturas td {
            border: none !important;
        }

        .assinaturas td {
            text-align: center;
            padding-top: 50px;
            font-size: 11px;
        }

        .assinatura-linha {
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto 5px auto;
        }

        main {
            margin-top: 0;
        }

        .no-print {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
@if(!($isPdf ?? false))
<div class="no-print" style="display:flex; gap:10px;">
    <a href="{{ url()->previous() }}" class="btn btn-secondary">
        Voltar
    </a>

    <a href="{{ request()->fullUrlWithQuery(['pdf' => 1]) }}" target="_blank" class="btn btn-danger">
        PDF
    </a>
</div>
@endif

<header>
    @if(!empty($reportLayout['header_html']))
        {!! $reportLayout['header_html'] !!}
    @else
        <table class="logos">
            <tr>
                <td width="33%">
                    <img src="{{ $isPdf ? imageToBase64(public_path('images/ifrs.png')) : asset('images/ifrs.png') }}">
                </td>
                <td width="33%">
                    <img src="{{ $isPdf ? imageToBase64(public_path('images/mulheresmil.jpg')) : asset('images/mulheresmil.jpg') }}">
                </td>
                <td width="33%">
                    <img src="{{ $isPdf ? imageToBase64(public_path('images/proex.png')) : asset('images/proex.png') }}">
                </td>
            </tr>
        </table>

        <h3>INSTITUTO FEDERAL DE EDUCACAO, CIENCIA E TECNOLOGIA DO RIO GRANDE DO SUL</h3>
        <h4>PROEX - Pro-Reitoria de Extensao</h4>
        <h4>Programa Mulheres Mil - Educacao, Cidadania e Desenvolvimento Sustentavel</h4>
    @endif

    @yield('header-extra')
</header>

<main>
    @yield('content')
</main>

<footer>
    @if(!empty($reportLayout['footer_html']))
        {!! $reportLayout['footer_html'] !!}
    @endif

    @if($isPdf ?? false)
        <div>Gerado em {{ now()->format('d/m/Y H:i') }}</div>
    @endif

    <div>2026 - ProBolsas - Sistema Administrativo de Gestão de Bolsas, Frequências e Pagamentos - {{ $currentVersion }}</div>
</footer>

@if($isPdf ?? false)
<script type="text/php">
    if (isset($pdf)) {
        $pdf->page_text(270, 820, "Pagina {PAGE_NUM} de {PAGE_COUNT}", null, 8, array(0,0,0));
    }
</script>
@endif
</body>
</html>

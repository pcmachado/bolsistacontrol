<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Relatório')</title>

    <style>

        @if($isPdf ?? false)

        /* =========================
        PDF (DOMPDF)
        ========================= */

        @page {
            margin: 220px 40px 90px 40px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        /* HEADER FIXO */
        header {
            position: fixed;
            top: -220px;
            left: 0;
            right: 0;
            text-align: center;
        }

        /* FOOTER FIXO */
        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
        }

        /* EVITA QUEBRA INTERNA */
        .no-break {
            page-break-inside: avoid;
        }

        /* QUEBRA MANUAL */
        .page-break {
            page-break-after: always;
        }

        @else

        /* =========================
        HTML (NAVEGADOR)
        ========================= */

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

        /* =========================
        TIPOGRAFIA HEADER
        ========================= */

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

        /* =========================
        LOGOS
        ========================= */

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

        /* =========================
        TABELAS
        ========================= */

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

        /* EVITA QUEBRA DE LINHA EM TABELAS IMPORTANTES */
        table.no-break {
            page-break-inside: avoid;
        }

        /* =========================
        ASSINATURAS
        ========================= */

        .assinaturas,
        .assinaturas td {
            border: none !important;
        }

        .assinaturas td {
            text-align: center;
            padding-top: 50px;
            font-size: 11px;
        }

        /* LINHA BONITA DE ASSINATURA */
        .assinatura-linha {
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto 5px auto;
        }

        /* =========================
        CONTEÚDO
        ========================= */

        main {
            margin-top: 0px;
        }

        /* =========================
        BOTÕES (HTML)
        ========================= */

        .no-print {
            margin-bottom: 15px;
        }

        /* =========================
        PAGINAÇÃO
        ========================= */

    </style>

</head>
<body>

{{-- BOTÕES --}}
@if(!($isPdf ?? false))
<div class="no-print" style="display:flex; gap:10px;">

    <a href="{{ url()->previous() }}" class="btn btn-secondary">
        ← Voltar
    </a>

    <a href="{{ request()->fullUrlWithQuery(['pdf' => 1]) }}"
       target="_blank"
       class="btn btn-danger">
        📄 PDF
    </a>

</div>
@endif

{{-- HEADER --}}
<header>

    <table class="logos">
        <tr>
            <td width="33%">
                <img src="{{ $isPdf
                    ? imageToBase64(public_path('images/ifrs.png'))
                    : asset('images/ifrs.png') }}">
            </td>

            <td width="33%">
                <img src="{{ $isPdf
                    ? imageToBase64(public_path('images/mulheresmil.jpg'))
                    : asset('images/mulheresmil.jpg') }}">
            </td>

            <td width="33%">
                <img src="{{ $isPdf
                    ? imageToBase64(public_path('images/proex.png'))
                    : asset('images/proex.png') }}">
            </td>
        </tr>
    </table>

    <h3>INSTITUTO FEDERAL DE EDUCAÇÃO, CIÊNCIA E TECNOLOGIA DO RIO GRANDE DO SUL</h3>
    <h4>PROEX – Pró-Reitoria de Extensão</h4>
    <h4>Programa Mulheres Mil – Educação, Cidadania e Desenvolvimento Sustentável</h4>

    @yield('header-extra')

</header>

{{-- CONTEÚDO --}}
<main>
    @yield('content')
</main>

<footer>
    @if($isPdf ?? false)
        Gerado em {{ now()->format('d/m/Y H:i') }}
    @endif
</footer>

@if($isPdf ?? false)
<script type="text/php">
    if (isset($pdf)) {
        $pdf->page_text(270, 820, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 8, array(0,0,0));
    }
</script>
@endif

</body>
</html>
@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="page-header mb-4">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h1 class="page-title mb-1">
                    {{ $template->name }}
                </h1>

                <p class="text-muted mb-0">
                    {{ $template->description }}
                </p>

            </div>

            <a href="{{ route('admin.document-templates.edit', $template) }}"
               class="btn btn-primary">

                <i class="bi bi-pencil"></i>
                Editar

            </a>

        </div>

    </div>

    <div class="row g-4">

        <div class="col-lg-8">

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        Preview
                    </h5>
                </div>

                <div class="card-body p-0">

                    <iframe
                        srcdoc="{{ $template->renderHtml() }}"
                        style="width:100%; height:900px; border:0;"
                    ></iframe>

                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        Informações
                    </h5>
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <strong>Key</strong><br>
                        <code>{{ $template->key }}</code>
                    </div>

                    <div class="mb-3">
                        <strong>Status</strong><br>

                        @if($template->active)
                            <span class="badge bg-success">
                                Ativo
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                Inativo
                            </span>
                        @endif

                    </div>

                    <div class="mb-3">
                        <strong>Instituição</strong><br>
                        {{ $template->institution?->name ?? 'Global' }}
                    </div>

                    <div>
                        <strong>Unidade</strong><br>
                        {{ $template->unit?->name ?? 'Todas' }}
                    </div>

                </div>

            </div>

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        Tokens disponíveis
                    </h5>
                </div>

                <div class="card-body">

                    @foreach($template->defaultPreviewTokens() as $token => $value)

                        <div class="border rounded p-2 mb-2">

                            <code>{{ $token }}</code>

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
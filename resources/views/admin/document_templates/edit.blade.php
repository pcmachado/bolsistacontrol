@extends('layouts.app')

@section('title', 'Editar Template')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-file-earmark-richtext me-2"></i>
                Editar Template - {{ $template->name }}
            </h3>
            <p class="text-muted mb-0">Configure o documento, seus logotipos de cabeçalho e acompanhe a prévia em tempo real.</p>
        </div>

        <a href="{{ route('admin.document-templates.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Voltar
        </a>
    </div>

    <form id="templateForm" method="POST" action="{{ route('admin.document-templates.update', $template) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-xl-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <strong>Dados gerais</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nome</label>
                                <input name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $template->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Descrição</label>
                                <input name="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description', $template->description) }}">
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Instituição</label>
                                <select name="institution_id" class="form-select @error('institution_id') is-invalid @enderror">
                                    <option value="">Global</option>
                                    @foreach($institutions as $i)
                                        <option value="{{ $i->id }}" @selected(old('institution_id', $template->institution_id) == $i->id)>
                                            {{ $i->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('institution_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Unidade</label>
                                <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
                                    <option value="">Todas</option>
                                    @foreach($units as $u)
                                        <option value="{{ $u->id }}" @selected(old('unit_id', $template->unit_id) == $u->id)>
                                            {{ $u->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>Cabeçalho visual</strong>
                        <span class="badge bg-light text-dark">3 seções de logotipo</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Título do documento</label>
                                <input id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $template->title) }}" data-preview-sync>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Subtítulo</label>
                                <input id="subtitle" name="subtitle" class="form-control @error('subtitle') is-invalid @enderror" value="{{ old('subtitle', $template->subtitle) }}" data-preview-sync>
                                @error('subtitle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @foreach(['left' => 'Esquerda', 'center' => 'Centro', 'right' => 'Direita'] as $position => $label)
                                <div class="col-md-4">
                                    <label class="form-label">Logo {{ $label }}</label>
                                    <input type="file"
                                           name="header_{{ $position }}_logo"
                                           accept="image/*"
                                           class="form-control @error('header_'.$position.'_logo') is-invalid @enderror"
                                           data-logo-position="{{ $position }}">
                                    @error('header_'.$position.'_logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <div class="border rounded p-2 mt-2 text-center bg-light">
                                        @if($template->headerLogoUrl($position))
                                            <img src="{{ $template->headerLogoUrl($position) }}" alt="Logo {{ strtolower($label) }} atual" class="img-fluid" style="max-height: 56px;">
                                        @else
                                            <small class="text-muted">Sem logo</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <strong>Conteúdo HTML</strong>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Cabeçalho complementar (HTML)</label>
                            <textarea id="header_html" name="header_html" rows="4" class="form-control sgb-tinymce @error('header_html') is-invalid @enderror">{{ old('header_html', $template->header_html) }}</textarea>
                            <small class="text-muted">Use este campo apenas para textos extras do cabeçalho; os logotipos já são tratados acima.</small>
                            @error('header_html')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Corpo do documento (HTML)</label>
                            <textarea id="body_html" name="body_html" rows="8" class="form-control sgb-tinymce @error('body_html') is-invalid @enderror" required>{{ old('body_html', $template->body_html) }}</textarea>
                            @error('body_html')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Rodapé (HTML)</label>
                            <textarea id="footer_html" name="footer_html" rows="4" class="form-control sgb-tinymce @error('footer_html') is-invalid @enderror">{{ old('footer_html', $template->footer_html) }}</textarea>
                            @error('footer_html')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="active" value="1" class="form-check-input" @checked(old('active', $template->active))>
                            <label class="form-check-label">Template ativo</label>
                        </div>

                        <div class="d-flex flex-wrap justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary" onclick="refreshPreview()">
                                <i class="bi bi-arrow-clockwise"></i>
                                Atualizar prévia
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="previewPDF()">
                                <i class="bi bi-file-earmark-pdf"></i>
                                Pré-visualizar PDF
                            </button>
                            <button class="btn btn-success">
                                <i class="bi bi-check-lg"></i>
                                Salvar Template
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card shadow-sm sticky-xl-top" style="top: 1rem;">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>Preview do documento</strong>
                        <small class="text-muted">A4 aproximado</small>
                    </div>
                    <div class="card-body bg-light">
                        <div id="documentPreview" class="document-preview bg-white shadow-sm mx-auto">
                            {!! $previewHtml !!}
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <strong>Variáveis disponíveis no documento:</strong>
                    <ul class="mb-0">
                        <li><code>{{ '{{ scholarship_holder }}' }}</code> - Nome do bolsista</li>
                        <li><code>{{ '{{ cpf }}' }}</code> - CPF</li>
                        <li><code>{{ '{{ project }}' }}</code> - Projeto</li>
                        <li><code>{{ '{{ amount }}' }}</code> - Valor</li>
                        <li><code>{{ '{{ period }}' }}</code> - Mês/Ano</li>
                        <li><code>{{ '{{ institution }}' }}</code> - Instituição</li>
                        <li><code>{{ '{{ unit }}' }}</code> - Unidade</li>
                        <li><code>{{ '{{ generated_at }}' }}</code> - Data de geração</li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    .document-preview {
        color: #212529;
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 12px;
        line-height: 1.45;
        max-width: 794px;
        min-height: 560px;
        overflow: auto;
        padding: 32px;
    }

    .document-preview img {
        max-width: 100%;
    }
</style>
@endpush

@push('scripts')
<script>
const savedLogoUrls = {
    left: @json($template->headerLogoUrl('left')),
    center: @json($template->headerLogoUrl('center')),
    right: @json($template->headerLogoUrl('right')),
};
const selectedLogoUrls = { ...savedLogoUrls };
const previewTokens = @json($template->defaultPreviewTokens());

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

tinymce.init({
    selector: 'textarea.sgb-tinymce',
    height: 260,
    menubar: false,
    plugins: ['lists', 'link', 'table', 'code', 'paste'],
    toolbar: 'undo redo | bold italic underline | bullist numlist | link table | code',
    paste_as_text: true,
    branding: false,
    setup(editor) {
        editor.on('keyup change input undo redo setcontent', () => refreshPreview());
    },
    init_instance_callback() {
        refreshPreview();
    }
});

function logoCell(position, align, label) {
    const url = selectedLogoUrls[position];
    const content = url
        ? `<img src="${escapeHtml(url)}" alt="Logo ${escapeHtml(label)}" style="max-height: 64px; max-width: 150px; object-fit: contain;">`
        : '&nbsp;';

    return `<div style="display: table-cell; width: ${position === 'center' ? '50%' : '25%'}; vertical-align: middle; text-align: ${align};">${content}</div>`;
}

function structuredHeaderHtml() {
    const title = document.getElementById('title').value.trim();
    const subtitle = document.getElementById('subtitle').value.trim();
    const hasLogo = Object.values(selectedLogoUrls).some(Boolean);

    if (!hasLogo && !title && !subtitle) {
        return '';
    }

    const titleHtml = title ? `<div style="font-size: 17px; font-weight: 700;">${escapeHtml(title)}</div>` : '';
    const subtitleHtml = subtitle ? `<div style="font-size: 12px; color: #555;">${escapeHtml(subtitle)}</div>` : '';

    return `<div style="display: table; width: 100%; margin-bottom: 18px; border-bottom: 1px solid #d9dee3; padding-bottom: 10px;">
        <div style="display: table-row;">
            ${logoCell('left', 'left', 'esquerda')}
            ${logoCell('center', 'center', 'central')}
            ${logoCell('right', 'right', 'direita')}
        </div>
        ${(titleHtml || subtitleHtml) ? `<div style="display: table-caption; caption-side: bottom; text-align: center; padding-top: 8px;">${titleHtml}${subtitleHtml}</div>` : ''}
    </div>`;
}

function editorContent(field) {
    const editor = tinymce.get(field);
    return editor ? editor.getContent() : document.getElementById(field).value;
}

function applyPreviewTokens(html) {
    return Object.entries(previewTokens).reduce((current, [token, value]) => {
        return current.split(token).join(value);
    }, html);
}

function refreshPreview() {
    const html = structuredHeaderHtml()
        + editorContent('header_html')
        + editorContent('body_html')
        + editorContent('footer_html');

    document.getElementById('documentPreview').innerHTML = applyPreviewTokens(html);
}

document.querySelectorAll('[data-preview-sync]').forEach((field) => {
    field.addEventListener('input', refreshPreview);
});

document.querySelectorAll('[data-logo-position]').forEach((field) => {
    field.addEventListener('change', (event) => {
        const [file] = event.target.files;
        const position = event.target.dataset.logoPosition;

        selectedLogoUrls[position] = file ? URL.createObjectURL(file) : savedLogoUrls[position];
        refreshPreview();
    });
});

function previewPDF() {
    let previewForm = document.createElement('form');
    previewForm.method = 'POST';
    previewForm.action = "{{ route('admin.document-templates.preview', $template) }}";
    previewForm.target = '_blank';

    const token = document.querySelector('input[name=_token]').cloneNode();
    previewForm.appendChild(token);

    ['title', 'subtitle'].forEach(field => {
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = field;
        input.value = document.getElementById(field).value;
        previewForm.appendChild(input);
    });

    ['header_html', 'body_html', 'footer_html'].forEach(field => {
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = field;
        input.value = editorContent(field);
        previewForm.appendChild(input);
    });

    document.body.appendChild(previewForm);
    previewForm.submit();
    previewForm.remove();
}
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Editar Template')

@section('content')
<div class="container">
    <h3 class="mb-4">
        Editar Template - {{ $template->name }}
    </h3>

    <form id="templateForm" method="POST" action="{{ route('admin.document-templates.update', $template) }}">
        @csrf
        @method('PUT')

        <button type="button" class="btn btn-secondary mb-3" onclick="previewPDF()">
            <i class="bi bi-file-earmark-pdf"></i>
            Pre-visualizar PDF
        </button>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Nome</label>
                <input name="name" class="form-control" value="{{ $template->name }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Descricao</label>
                <input name="description" class="form-control" value="{{ $template->description }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Instituicao</label>
                <select name="institution_id" class="form-select">
                    <option value="">Global</option>
                    @foreach($institutions as $i)
                        <option value="{{ $i->id }}" @selected($template->institution_id == $i->id)>
                            {{ $i->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Unidade</label>
                <select name="unit_id" class="form-select">
                    <option value="">Todas</option>
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" @selected($template->unit_id == $u->id)>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <hr>

        <div class="mb-3">
            <label class="form-label">Cabecalho (HTML)</label>
            <textarea id="header_html" name="header_html" rows="4" class="form-control sgb-tinymce">{{ $template->header_html }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Corpo do Documento (HTML)</label>
            <textarea id="body_html" name="body_html" rows="8" class="form-control sgb-tinymce">{{ $template->body_html }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Rodape (HTML)</label>
            <textarea id="footer_html" name="footer_html" rows="4" class="form-control sgb-tinymce">{{ $template->footer_html }}</textarea>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="active" value="1" class="form-check-input" @checked($template->active)>
            <label class="form-check-label">Template ativo</label>
        </div>

        <div class="d-flex justify-content-end">
            <button class="btn btn-success">Salvar Template</button>
        </div>
    </form>

    <div class="alert alert-info mt-4">
        <strong>Variaveis disponiveis no documento:</strong>
        <ul class="mb-0">
            <li><code>{{ '{{ scholarship_holder }}' }}</code> - Nome do bolsista</li>
            <li><code>{{ '{{ cpf }}' }}</code> - CPF</li>
            <li><code>{{ '{{ project }}' }}</code> - Projeto</li>
            <li><code>{{ '{{ amount }}' }}</code> - Valor</li>
            <li><code>{{ '{{ period }}' }}</code> - Mes/Ano</li>
            <li><code>{{ '{{ institution }}' }}</code> - Instituicao</li>
            <li><code>{{ '{{ unit }}' }}</code> - Unidade</li>
            <li><code>{{ '{{ generated_at }}' }}</code> - Data de geracao</li>
        </ul>
    </div>
</div>

@push('scripts')
<script>
tinymce.init({
    selector: 'textarea.sgb-tinymce',
    height: 300,
    menubar: false,
    plugins: ['lists', 'link', 'table', 'code', 'paste'],
    toolbar: 'undo redo | bold italic underline | bullist numlist | link table | code',
    paste_as_text: true,
    branding: false
});
</script>
<script>
function previewPDF() {
    let previewForm = document.createElement('form');
    previewForm.method = 'POST';
    previewForm.action = "{{ route('admin.document-templates.preview') }}";
    previewForm.target = '_blank';

    const token = document.querySelector('input[name=_token]').cloneNode();
    previewForm.appendChild(token);

    ['header_html', 'body_html', 'footer_html'].forEach(field => {
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = field;
        input.value = tinymce.get(field) ? tinymce.get(field).getContent() : '';
        previewForm.appendChild(input);
    });

    document.body.appendChild(previewForm);
    previewForm.submit();
    previewForm.remove();
}
</script>
@endpush
@endsection

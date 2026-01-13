@extends('layouts.app')

@section('title', 'Editar Template')

@section('content')
<div class="container">

    <h3 class="mb-4">
        Editar Template — {{ $template->name }}
    </h3>

    <form method="POST"
          action="{{ route('admin.document-templates.update', $template) }}">
        @csrf
        @method('PUT')

        <button type="button" class="btn btn-secondary" onclick="previewPDF()">
            <i class="bi bi-file-earmark-pdf"></i>
            Pré-visualizar PDF
        </button>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Nome</label>
                <input name="name" class="form-control"
                       value="{{ $template->name }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">Descrição</label>
                <input name="description" class="form-control"
                       value="{{ $template->description }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Instituição</label>
                <select name="institution_id" class="form-select">
                    <option value="">Global</option>
                    @foreach($institutions as $i)
                        <option value="{{ $i->id }}"
                            @selected($template->institution_id == $i->id)>
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
                        <option value="{{ $u->id }}"
                            @selected($template->unit_id == $u->id)>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <hr>

        <div class="mb-3">
            <label class="form-label">Cabeçalho (HTML)</label>
            <textarea name="header_html wysiwyg"
                      rows="4"
                      class="form-control">{{ $template->header_html }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Corpo do Documento (HTML)</label>
            <textarea name="body_html wysiwyg"
                      rows="8"
                      class="form-control">{{ $template->body_html }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Rodapé (HTML)</label>
            <textarea name="footer_html wysiwyg"
                      rows="4"
                      class="form-control">{{ $template->footer_html }}</textarea>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="active" value="1"
                   class="form-check-input"
                   @checked($template->active)>
            <label class="form-check-label">Template ativo</label>
        </div>

        <div class="d-flex justify-content-end">
            <button class="btn btn-success">
                Salvar Template
            </button>
        </div>

    </form>

    <div class="alert alert-info mt-4">
        <strong>Variáveis disponíveis no documento:</strong>
        <ul class="mb-0">
            <li><code>{{ '{{ scholarship_holder }}' }}</code> — Nome do bolsista</li>
            <li><code>{{ '{{ cpf }}' }}</code> — CPF</li>
            <li><code>{{ '{{ project }}' }}</code> — Projeto</li>
            <li><code>{{ '{{ amount }}' }}</code> — Valor</li>
            <li><code>{{ '{{ period }}' }}</code> — Mês/Ano</li>
            <li><code>{{ '{{ institution }}' }}</code> — Instituição</li>
            <li><code>{{ '{{ unit }}' }}</code> — Unidade</li>
            <li><code>{{ '{{ generated_at }}' }}</code> — Data de geração</li>
        </ul>
    </div>

</div>

@push('scripts')
<script>
tinymce.init({
    selector: 'textarea.wysiwyg',
    height: 250,
    menubar: false,
    plugins: [
        'lists',
        'link',
        'table',
        'code',
        'paste'
    ],
    toolbar: `
        undo redo |
        bold italic underline |
        bullist numlist |
        link table |
        code
    `,
    paste_as_text: true,
    branding: false
});
</script>
<script>
function previewPDF() {
    const form = document.getElementById('templateForm');

    // Criar formulário temporário (não salva dados!)
    let previewForm = document.createElement('form');
    previewForm.method = "POST";
    previewForm.action = "{{ route('admin.document-templates.preview') }}";
    previewForm.target = "_blank";

    // Token CSRF
    const token = document.querySelector('input[name=_token]').cloneNode();
    previewForm.appendChild(token);

    // Copiar campos relevantes
    ['header_html','body_html','footer_html'].forEach(field => {
        let input = document.createElement('input');
        input.type = "hidden";
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

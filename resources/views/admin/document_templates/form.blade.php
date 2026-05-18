<div class="row g-4">

    {{-- FORMULÁRIO --}}
    <div class="col-lg-7">

        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white border-0">
                <h5 class="mb-0">
                    Informações gerais
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-8">
                        <label class="form-label">
                            Nome do template
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $template->name ?? '') }}"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Chave
                        </label>

                        <input
                            type="text"
                            name="key"
                            class="form-control"
                            value="{{ old('key', $template->key ?? '') }}"
                            {{ isset($template) ? 'readonly' : '' }}
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label">
                            Descrição
                        </label>

                        <textarea
                            name="description"
                            rows="2"
                            class="form-control"
                        >{{ old('description', $template->description ?? '') }}</textarea>
                    </div>

                </div>

            </div>

        </div>

        {{-- IDENTIDADE VISUAL --}}
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white border-0">
                <h5 class="mb-0">
                    Cabeçalho institucional
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label">
                            Logo esquerda
                        </label>

                        <input
                            type="file"
                            name="header_left_logo"
                            class="form-control"
                        >

                        @if(!empty($template?->header_left_logo_path))
                            <img
                                src="{{ $template->headerLogoUrl('left') }}"
                                class="img-fluid mt-2 rounded border"
                                style="max-height:80px;"
                            >
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Logo centro
                        </label>

                        <input
                            type="file"
                            name="header_center_logo"
                            class="form-control"
                        >

                        @if(!empty($template?->header_center_logo_path))
                            <img
                                src="{{ $template->headerLogoUrl('center') }}"
                                class="img-fluid mt-2 rounded border"
                                style="max-height:80px;"
                            >
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Logo direita
                        </label>

                        <input
                            type="file"
                            name="header_right_logo"
                            class="form-control"
                        >

                        @if(!empty($template?->header_right_logo_path))
                            <img
                                src="{{ $template->headerLogoUrl('right') }}"
                                class="img-fluid mt-2 rounded border"
                                style="max-height:80px;"
                            >
                        @endif
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">
                            Título
                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            value="{{ old('title', $template->title ?? '') }}"
                        >
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">
                            Subtítulo
                        </label>

                        <input
                            type="text"
                            name="subtitle"
                            class="form-control"
                            value="{{ old('subtitle', $template->subtitle ?? '') }}"
                        >
                    </div>

                </div>

            </div>

        </div>

        {{-- HTML --}}
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white border-0">
                <h5 class="mb-0">
                    Conteúdo HTML
                </h5>
            </div>

            <div class="card-body">

                <div class="mb-4">

                    <label class="form-label">
                        Cabeçalho adicional
                    </label>

                    <textarea
                        name="header_html"
                        class="form-control tinymce"
                        rows="6"
                    >{{ old('header_html', $template->header_html ?? '') }}</textarea>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Corpo principal
                    </label>

                    <textarea
                        name="body_html"
                        class="form-control tinymce"
                        rows="12"
                    >{{ old('body_html', $template->body_html ?? '') }}</textarea>

                </div>

                <div>

                    <label class="form-label">
                        Rodapé
                    </label>

                    <textarea
                        name="footer_html"
                        class="form-control tinymce"
                        rows="6"
                    >{{ old('footer_html', $template->footer_html ?? '') }}</textarea>

                </div>

            </div>

        </div>

        {{-- ESCOPOS --}}
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white border-0">
                <h5 class="mb-0">
                    Escopo
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">
                            Instituição
                        </label>

                        <select
                            name="institution_id"
                            class="form-select"
                        >
                            <option value="">
                                Global
                            </option>

                            @foreach($institutions as $institution)

                                <option
                                    value="{{ $institution->id }}"
                                    @selected(old('institution_id', $template->institution_id ?? '') == $institution->id)
                                >
                                    {{ $institution->name }}
                                </option>

                            @endforeach

                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Unidade
                        </label>

                        <select
                            name="unit_id"
                            class="form-select"
                        >
                            <option value="">
                                Todas
                            </option>

                            @foreach($units as $unit)

                                <option
                                    value="{{ $unit->id }}"
                                    @selected(old('unit_id', $template->unit_id ?? '') == $unit->id)
                                >
                                    {{ $unit->name }}
                                </option>

                            @endforeach

                        </select>
                    </div>

                    <div class="col-md-4">

                        <div class="form-check mt-4">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="active"
                                value="1"
                                @checked(old('active', $template->active ?? true))
                            >

                            <label class="form-check-label">
                                Template ativo
                            </label>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="d-flex justify-content-end gap-2 mb-5">

            <button
                type="button"
                class="btn btn-outline-secondary"
                id="preview-template"
            >
                <i class="bi bi-eye"></i>
                Atualizar preview
            </button>

            <button class="btn btn-primary">

                <i class="bi bi-check-circle"></i>
                Salvar template

            </button>

        </div>

    </div>

    {{-- TOKENS --}}
    <div class="col-lg-5">

        <div class="card shadow-sm border-0 sticky-top"
             style="top:20px;">

            <div class="card-header bg-white border-0">
                <h5 class="mb-0">
                    Tokens disponíveis
                </h5>
            </div>

            <div class="card-body">

                @foreach(($template?->defaultPreviewTokens() ?? \App\Models\DocumentTemplate::make()->defaultPreviewTokens()) as $token => $value)

                    <button
                        type="button"
                        class="btn btn-sm btn-light border w-100 text-start mb-2 token-btn"
                        data-token="{{ $token }}"
                    >
                        <code>{{ $token }}</code>
                    </button>

                @endforeach

            </div>

        </div>

    </div>

</div>

@push('scripts')

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"></script>

<script>

tinymce.init({
    selector: '.tinymce',
    height: 320,
    menubar: false,
    plugins: 'lists link table code image',
    toolbar:
        'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | table link | code',
});

document.querySelectorAll('.token-btn').forEach(button => {

    button.addEventListener('click', function () {

        const token = this.dataset.token;

        navigator.clipboard.writeText(token);

    });

});

document.getElementById('preview-template')
    ?.addEventListener('click', function () {

        tinymce.triggerSave();

        const form = this.closest('form');

        const formData = new FormData(form);

        fetch(
            "{{ isset($template) ? route('admin.document-templates.preview', $template) : '#' }}",
            {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN':
                        '{{ csrf_token() }}'
                }
            }
        )
        .then(response => response.blob())
        .then(blob => {

            const url = URL.createObjectURL(blob);

            window.open(url, '_blank');

        });

    });

</script>

@endpush
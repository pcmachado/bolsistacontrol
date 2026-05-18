@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="page-header mb-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

            <div>
                <h1 class="page-title mb-1">
                    Novo Template
                </h1>

                <p class="text-muted mb-0">
                    Configure layouts institucionais e documentos PDF dinâmicos.
                </p>
            </div>

            <a href="{{ route('admin.document-templates.index') }}"
               class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left"></i>
                Voltar

            </a>

        </div>

    </div>

    <form method="POST"
          action="{{ route('admin.document-templates.store') }}"
          enctype="multipart/form-data">

        @csrf

        @include('admin.document_templates.form')

    </form>

</div>

@endsection


@push('scripts')

{{-- TinyMCE --}}
<script
    src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"
    referrerpolicy="origin">
</script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    tinymce.init({

        selector: '.tinymce',

        height: 320,

        menubar: 'file edit view insert format tools table help',

        plugins: [
            'advlist',
            'autolink',
            'lists',
            'link',
            'image',
            'charmap',
            'preview',
            'anchor',
            'searchreplace',
            'visualblocks',
            'code',
            'fullscreen',
            'insertdatetime',
            'media',
            'table',
            'help',
            'wordcount'
        ],

        toolbar:
            'undo redo | ' +
            'blocks fontfamily fontsize | ' +
            'bold italic underline forecolor backcolor | ' +
            'alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | ' +
            'table link image media | ' +
            'removeformat code preview fullscreen',

        content_style:
            'body {' +
            'font-family:Helvetica,Arial,sans-serif;' +
            'font-size:14px' +
            '}',

        branding: false,

        promotion: false,

        language: 'pt_BR',

    });

});


/*
|--------------------------------------------------------------------------
| TOKENS
|--------------------------------------------------------------------------
*/

document.querySelectorAll('.token-btn').forEach(button => {

    button.addEventListener('click', function () {

        const token = this.dataset.token;

        navigator.clipboard.writeText(token);

        this.classList.remove('btn-light');

        this.classList.add('btn-success');

        setTimeout(() => {

            this.classList.remove('btn-success');

            this.classList.add('btn-light');

        }, 800);

    });

});


/*
|--------------------------------------------------------------------------
| PREVIEW PDF
|--------------------------------------------------------------------------
*/

document.getElementById('preview-template')
    ?.addEventListener('click', function () {

        tinymce.triggerSave();

        const form = this.closest('form');

        const formData = new FormData(form);

        fetch(
            "{{ route('admin.document-templates.preview') }}",
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

        })
        .catch(error => {

            console.error(error);

            alert('Erro ao gerar preview.');

        });

    });

</script>

@endpush
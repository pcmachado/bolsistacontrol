<script
    src="https://cdn.tiny.cloud/1/{{ config('services.tinymce.api_key') }}/tinymce/6/tinymce.min.js"
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

        branding: false,

        promotion: false,

        language: 'pt_BR',

    });

});

</script>
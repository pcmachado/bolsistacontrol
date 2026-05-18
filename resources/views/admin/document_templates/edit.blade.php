@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="page-header mb-4">
        <h1 class="page-title">
            Editar Template
        </h1>
    </div>

    <form method="POST"
          action="{{ route('admin.document-templates.update', $template) }}"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        @include('admin.document_templates.form')

    </form>

</div>

@endsection
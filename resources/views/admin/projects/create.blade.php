@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Criar novo Projeto</h2>
        </div>
        <div class="mb-3">
            <a href="{{ route('admin.projects.create.step1') }}" class="btn btn-outline-primary">
                <i class="bi bi-magic"></i> Criar Projeto com Assistente
            </a>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary btn-sm mb-2" href="{{ route('admin.projects.index') }}"><i class="fa fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Whoops!</strong> Ocorreu um problema com sua entrada.
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label"><strong>Nome</strong></label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
        </div>

        <div class="col-md-6">
            <label class="form-label"><strong>Descricao</strong></label>
            <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
        </div>

        <div class="col-md-4">
            <label class="form-label"><strong>Unidade</strong></label>
            <select name="unit_id" class="form-select">
                <option value="">Selecione uma unidade</option>
                @foreach ($units as $unit)
                    <option value="{{ $unit->id }}" @selected(old('unit_id') == $unit->id)>{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label"><strong>Instituicao</strong></label>
            <select name="institution_id" class="form-select">
                <option value="">Selecione uma instituicao</option>
                @foreach ($institutions as $institution)
                    <option value="{{ $institution->id }}" @selected(old('institution_id') == $institution->id)>{{ $institution->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label"><strong>Inicio</strong></label>
            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
        </div>

        <div class="col-md-2">
            <label class="form-label"><strong>Final</strong></label>
            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
        </div>
    </div>

    <hr class="my-4">

    <h5>Branding e Relatorios</h5>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label"><strong>Titulo do cabecalho</strong></label>
            <input type="text" name="report_title" class="form-control" value="{{ old('report_title') }}">
        </div>

        <div class="col-md-6">
            <label class="form-label"><strong>Subtitulo do cabecalho</strong></label>
            <input type="text" name="report_subtitle" class="form-control" value="{{ old('report_subtitle') }}">
        </div>

        <div class="col-md-6">
            <label class="form-label"><strong>Logo do projeto</strong></label>
            <input type="file" name="report_logo" accept="image/*" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label"><strong>Modelo de documento</strong></label>
            <select name="document_template_id" class="form-select">
                <option value="">Sem modelo adicional</option>
                @foreach ($templates as $template)
                    <option value="{{ $template->id }}" @selected(old('document_template_id') == $template->id)>
                        {{ $template->name }} ({{ $template->key }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label"><strong>Modelo de documento mensal</strong></label>
            <select name="monthly_report_template_id" class="form-select">
                <option value="">Sem modelo mensal</option>
                @foreach ($templates as $template)
                    <option value="{{ $template->id }}" @selected(old('monthly_report_template_id') == $template->id)>
                        {{ $template->name }} ({{ $template->key }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label"><strong>Modelo de documento final</strong></label>
            <select name="final_report_template_id" class="form-select">
                <option value="">Sem modelo final</option>
                @foreach ($templates as $template)
                    <option value="{{ $template->id }}" @selected(old('final_report_template_id') == $template->id)>
                        {{ $template->name }} ({{ $template->key }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-12">
            <label class="form-label"><strong>Cabecalho customizado (HTML)</strong></label>
            <textarea name="report_header_html" rows="4" class="form-control">{{ old('report_header_html') }}</textarea>
        </div>

        <div class="col-12">
            <label class="form-label"><strong>Rodape customizado (HTML)</strong></label>
            <textarea name="report_footer_html" rows="3" class="form-control">{{ old('report_footer_html') }}</textarea>
        </div>
    </div>

    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-floppy-disk"></i> Enviar
        </button>
    </div>
</form>
@endsection

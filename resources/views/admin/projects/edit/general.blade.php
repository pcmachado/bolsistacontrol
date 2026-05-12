@extends('layouts.app')

@section('title', 'Editar Projeto')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">
        Editar Projeto: <strong>{{ $project->name }}</strong>
    </h4>

    <div class="row">
        <div class="col-md-3">
            @include('admin.projects.edit._nav', ['active' => 'general'])
        </div>

        <div class="col-md-9">
            <div class="card rounded-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.projects.update', $project) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nome do Projeto</label>
                                <input type="text" name="name" value="{{ old('name', $project->name) }}" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Instituicao</label>
                                <select name="institution_id" class="form-select">
                                    @foreach($institutions as $institution)
                                        <option value="{{ $institution->id }}" @selected(old('institution_id', $project->institution_id) == $institution->id)>
                                            {{ $institution->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Descricao</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description', $project->description) }}</textarea>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Data de Inicio</label>
                                <input type="date" name="start_date" value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Data de Termino</label>
                                <input type="date" name="end_date" value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Titulo do Cabecalho</label>
                                <input type="text" name="report_title" value="{{ old('report_title', $project->report_title) }}" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Subtitulo do Cabecalho</label>
                                <input type="text" name="report_subtitle" value="{{ old('report_subtitle', $project->report_subtitle) }}" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Logo do Projeto</label>
                                <input type="file" name="report_logo" accept="image/*" class="form-control">
                                @if($project->report_logo_path)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/'.$project->report_logo_path) }}" alt="Logo atual do projeto" style="max-height: 72px;">
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Modelo de Documento</label>
                                <select name="document_template_id" class="form-select">
                                    <option value="">Sem modelo adicional</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}" @selected(old('document_template_id', $project->document_template_id) == $template->id)>
                                            {{ $template->name }} ({{ $template->key }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Modelo de Documento Mensal</label>
                                <select name="monthly_report_template_id" class="form-select">
                                    <option value="">Sem modelo mensal</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}" @selected(old('monthly_report_template_id', $project->monthly_report_template_id) == $template->id)>
                                            {{ $template->name }} ({{ $template->key }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Modelo de Documento Final</label>
                                <select name="final_report_template_id" class="form-select">
                                    <option value="">Sem modelo final</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}" @selected(old('final_report_template_id', $project->final_report_template_id) == $template->id)>
                                            {{ $template->name }} ({{ $template->key }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Cabecalho Customizado (HTML)</label>
                                <textarea name="report_header_html" rows="4" class="form-control">{{ old('report_header_html', $project->report_header_html) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Rodape Customizado (HTML)</label>
                                <textarea name="report_footer_html" rows="3" class="form-control">{{ old('report_footer_html', $project->report_footer_html) }}</textarea>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-block mt-4">
                            <button class="btn btn-primary">Salvar Alteracoes</button>
                            <a href="{{ route('admin.projects.edit.index', $project) }}" class="btn btn-outline-secondary">
                                Voltar
                            </a>
                            <a href="{{ route('admin.projects.destroy', $project) }}" class="btn btn-danger" onclick="event.preventDefault(); if(confirm('Tem certeza que deseja excluir este projeto?')) { document.getElementById('delete-form').submit(); }">
                                Excluir Projeto
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="delete-form" action="{{ route('admin.projects.destroy', $project) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

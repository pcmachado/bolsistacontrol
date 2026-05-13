@extends('layouts.project-wizard')

@section('title', 'Projeto')

@php
    $isEdit = isset($project);
@endphp

@section('wizard-content')
<h4 class="mb-4 fw-bold">
    {{ $isEdit ? 'Editar Projeto' : 'Criar Projeto' }}
</h4>

<form method="POST"
      enctype="multipart/form-data"
      action="{{ $isEdit ? route('admin.projects.update.step1', $project) : route('admin.projects.store.step1') }}">
    @csrf

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nome do Projeto</label>
            <input type="text"
                   name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $project->name ?? '') }}"
                   required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Descricao</label>
            <textarea name="description"
                      rows="3"
                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $project->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Unidade</label>
            <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                <option value="">Selecione</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" @selected(old('unit_id', $project->unit_id ?? null) == $unit->id)>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
            @error('unit_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Instituicao</label>
            <select name="institution_id" class="form-select @error('institution_id') is-invalid @enderror" required>
                <option value="">Selecione</option>
                @foreach($institutions as $institution)
                    <option value="{{ $institution->id }}" @selected(old('institution_id', $project->institution_id ?? null) == $institution->id)>
                        {{ $institution->name }}
                    </option>
                @endforeach
            </select>
            @error('institution_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Data de Inicio</label>
            <input type="date"
                   name="start_date"
                   class="form-control @error('start_date') is-invalid @enderror"
                   value="{{ old('start_date', isset($project->start_date) ? $project->start_date->format('Y-m-d') : '') }}"
                   required>
            @error('start_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Data de Termino</label>
            <input type="date"
                   name="end_date"
                   class="form-control @error('end_date') is-invalid @enderror"
                   value="{{ old('end_date', isset($project->end_date) ? $project->end_date->format('Y-m-d') : '') }}">
            @error('end_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <hr class="my-4">

    <h5 class="mb-3">Branding e Documentos</h5>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Titulo do Cabecalho</label>
            <input type="text"
                   name="report_title"
                   class="form-control @error('report_title') is-invalid @enderror"
                   value="{{ old('report_title', $project->report_title ?? '') }}">
            @error('report_title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Subtitulo do Cabecalho</label>
            <input type="text"
                   name="report_subtitle"
                   class="form-control @error('report_subtitle') is-invalid @enderror"
                   value="{{ old('report_subtitle', $project->report_subtitle ?? '') }}">
            @error('report_subtitle')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Logo do Projeto</label>
            <input type="file"
                   name="report_logo"
                   accept="image/*"
                   class="form-control @error('report_logo') is-invalid @enderror">
            @error('report_logo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @if(!empty($project?->report_logo_path))
                <div class="mt-2">
                    <img src="{{ asset('storage/'.$project->report_logo_path) }}"
                         alt="Logo atual do projeto"
                         style="max-height: 72px;">
                </div>
            @endif
        </div>

        <div class="col-md-6">
            <label class="form-label">Modelo de Documento</label>
            <select name="document_template_id" class="form-select @error('document_template_id') is-invalid @enderror">
                <option value="">Sem modelo adicional</option>
                @foreach($templates as $template)
                    <option value="{{ $template->id }}" @selected(old('document_template_id', $project->document_template_id ?? null) == $template->id)>
                        {{ $template->name }} ({{ $template->key }})
                    </option>
                @endforeach
            </select>
            <small class="text-muted">O corpo do modelo sera inserido nos relatorios do projeto.</small>
            @error('document_template_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Modelo de Documento Mensal</label>
            <select name="monthly_report_template_id" class="form-select @error('monthly_report_template_id') is-invalid @enderror">
                <option value="">Sem modelo mensal</option>
                @foreach($templates as $template)
                    <option value="{{ $template->id }}" @selected(old('monthly_report_template_id', $project->monthly_report_template_id ?? null) == $template->id)>
                        {{ $template->name }} ({{ $template->key }})
                    </option>
                @endforeach
            </select>
            @error('monthly_report_template_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Modelo de Documento Final</label>
            <select name="final_report_template_id" class="form-select @error('final_report_template_id') is-invalid @enderror">
                <option value="">Sem modelo final</option>
                @foreach($templates as $template)
                    <option value="{{ $template->id }}" @selected(old('final_report_template_id', $project->final_report_template_id ?? null) == $template->id)>
                        {{ $template->name }} ({{ $template->key }})
                    </option>
                @endforeach
            </select>
            @error('final_report_template_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label class="form-label">Cabecalho Customizado (HTML)</label>
            <textarea name="report_header_html"
                      rows="4"
                      class="form-control @error('report_header_html') is-invalid @enderror">{{ old('report_header_html', $project->report_header_html ?? '') }}</textarea>
            <small class="text-muted">Opcional. Se vazio, o sistema usa o titulo, subtitulo e logo do projeto.</small>
            @error('report_header_html')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label class="form-label">Rodape Customizado (HTML)</label>
            <textarea name="report_footer_html"
                      rows="3"
                      class="form-control @error('report_footer_html') is-invalid @enderror">{{ old('report_footer_html', $project->report_footer_html ?? '') }}</textarea>
            @error('report_footer_html')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">
            Voltar para lista
        </a>

        <button type="submit" class="btn btn-primary">
            Proximo
        </button>
    </div>
</form>
@endsection

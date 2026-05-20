@extends('layouts.app')

@section('title', 'Nova Versão')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark">Cadastrar Notas da Versão</h1>
        <a href="{{ route('admin.system_releases.index') }}" class="btn btn-secondary shadow-sm">Voltar</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.system_releases.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Versão (Ex: v1.0.5) <span class="text-danger">*</span></label>
                        <input type="text" name="version" class="form-control" required value="{{ old('version') }}" placeholder="Deve ser igual à tag do Git">
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label fw-bold">Novidades (Aceita HTML) <span class="text-danger">*</span></label>
                        <textarea name="release_notes" class="form-control" rows="8" required placeholder="<ul><li>Nova funcionalidade X</li><li>Correção do erro Y</li></ul>">{{ old('release_notes') }}</textarea>
                        <small class="text-muted mt-1 d-block"><i class="bi bi-info-circle me-1"></i> Você pode usar tags HTML básicas como &lt;ul&gt;, &lt;li&gt;, &lt;b&gt;, &lt;br&gt; para formatar o texto no modal.</small>
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Salvar Versão</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

    <style>
        .nav-pills .nav-link.completed {
            background-color: #198754; /* verde */
            color: #fff;
        }
    </style>

@section('content')
    <div class="container">
        <h3>Passo 4: Definir Cursos</h3>
        @include('admin.projects.partials._steps', ['step' => 4, 'project' => $project ?? null])
        @include('admin.projects.partials._progress', ['progress' => 64, 'label' => 'Passo 4 de 6'])

        <form method="POST" action="{{ route('admin.projects.store.step4', $project) }}">
            @csrf
            <div class="mb-3">
                <label for="courses" class="form-label">Cursos</label>
                <div class="input-group">
                    <select name="courses[]" class="form-select" multiple>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                        + Novo
                    </button>
                </div>
                <small class="text-muted">Segure CTRL (ou CMD no Mac) para selecionar mais de um curso.</small>
            </div>
            <button type="submit" class="btn btn-primary">Avançar</button>
        </form>
    </div>

    {{-- Modal genérico para adicionar curso --}}
    <x-modal-add-generic 
        id="addCourseModal"
        title="Adicionar Curso"
        :route="route('admin.courses.store')"
        :fields="[
            ['name' => 'name', 'label' => 'Nome do Curso', 'type' => 'text', 'required' => true],
            ['name' => 'description', 'label' => 'Descrição', 'type' => 'textarea']
        ]"
    />
@endsection

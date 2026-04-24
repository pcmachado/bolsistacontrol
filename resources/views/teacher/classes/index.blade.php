@extends('layouts.app')

@section('title', 'Minhas Turmas')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <h4>Minhas Turmas</h4>

        <table class="table">
            <thead>
                <tr>
                    <th>Turma</th>
                    <th>Curso</th>
                    <th>Disciplina</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach($classes as $c)
                    <tr>
                        <td>{{ $c->classOffering->name }}</td>
                        <td>{{ $c->classOffering->course->name }}</td>
                        <td>{{ $c->discipline->name }}</td>

                        <td>
                            <a href="{{ route('teacher.classes.show', $c->class_offering_id) }}"
                            class="btn btn-primary btn-sm">
                                Acessar
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Selecione a instituição</h4>
    <form method="POST" action="{{ route('institution.set') }}">
        @csrf
        <div class="mb-3">
            <select name="institution_id" class="form-select">
                @foreach($institutions as $inst)
                    <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Confirmar</button>
    </form>
</div>
@endsection

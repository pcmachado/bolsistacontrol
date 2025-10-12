@php
    $user = Auth::user();
    $isAdmin = $user->hasRole('admin') || $user->hasRole('coordenador_geral');
    $isCoordinator = $user->hasRole('coordenador_adjunto');
    $isScholar = $user->hasRole('bolsista');
@endphp

<form method="GET" class="mb-4 flex flex-wrap gap-4">
    @if($isAdmin)
        <select name="project_id" class="form-select">
            <option value="">Projeto</option>
            @foreach($projects as $project)
                <option value="{{ request('project_id') == $project->id ? 'selected' : '' }}">{{ $project->name }}</option>
            @endforeach
        </select>

        <select name="unit_id" class="form-select">
            <option value="">Unidade</option>
            @foreach($units as $unit)
                <option value="{{ request('unit_id') == $unit->id ? 'selected' : '' }}">{{ $unit->name }}</option>
            @endforeach
        </select>
    @endif

    @if($isAdmin || $isCoordinator)
        <select name="role" class="form-select">
            <option value="">Cargo</option>
            <option value="monitor" {{ request('role') == 'monitor' ? 'selected' : '' }}>Monitor</option>
            <option value="pesquisador" {{ request('role') == 'pesquisador' ? 'selected' : '' }}>Pesquisador</option>
        </select>

        <select name="scholarship_holder_id" class="form-select">
            <option value="">Bolsista</option>
            @foreach($scholarship_holders as $holder)
                <option value="{{ request('scholarship_holder_id') == $holder->id ? 'selected' : '' }}">
                    {{ $holder->user->name }}
                </option>
            @endforeach
        </select>
    @endif

    @if($isScholar)
        <input type="month" name="month" class="form-input" value="{{ request('month') }}">
        <input type="date" name="start_date" class="form-input" value="{{ request('start_date') }}">
        <input type="date" name="end_date" class="form-input" value="{{ request('end_date') }}">
    @endif

    @if($isAdmin || $isCoordinator)
        <input type="month" name="month" class="form-input" value="{{ request('month') }}">
        <input type="date" name="start_date" class="form-input" value="{{ request('start_date') }}">
        <input type="date" name="end_date" class="form-input" value="{{ request('end_date') }}">
    @endif

    <button type="submit" class="btn btn-primary">Filtrar</button>
</form>

<form method="GET" action="{{ route('attendance.submissions.index') }}" class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            @unlessrole('bolsista')
                <div class="col-md-3">
                    <label for="filter-month" class="form-label mb-1">Mês</label>
                    <input
                        id="filter-month"
                        type="month"
                        name="month"
                        value="{{ request('month') }}"
                        class="form-control"
                    >
                </div>
            @endunlessrole

            <div class="col-md-3">
                <label for="filter-status" class="form-label mb-1">Status</label>
                <select id="filter-status" name="status" class="form-select">
                    <option value="all" @selected(request('status') === 'all')>Todos</option>
                    <option value="submitted" @selected(request('status') === 'submitted')>Enviadas</option>
                    <option value="approved" @selected(request('status') === 'approved')>Homologadas</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejeitadas</option>
                    <option value="late" @selected(request('status') === 'late')>Atrasadas</option>
                    <option value="draft" @selected(request('status') === 'draft')>Rascunhos</option>
                </select>
            </div>

            {{-- UNIDADE (somente se houver unidades) --}}
            @if(isset($units) && $units->isNotEmpty())
                <div class="col-md-3">
                    <label class="form-label">Unidade</label>
                    <select name="unit_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}"
                                @selected(request('unit_id') == $unit->id)>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i>
                    Filtrar
                </button>

                <a href="{{ route('attendance.submissions.index') }}" class="btn btn-outline-secondary">
                    Limpar
                </a>
            </div>
        </div>
    </div>
</form>

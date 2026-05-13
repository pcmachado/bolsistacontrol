@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Não foi possível salvar a forma de fomento.</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ $action }}">
            @csrf
            @if(($method ?? 'POST') !== 'POST')
                @method($method)
            @endif

            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Nome</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $fundingSource->name) }}"
                        class="form-control @error('name') is-invalid @enderror"
                        required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Código</label>
                    <input
                        type="text"
                        name="code"
                        value="{{ old('code', $fundingSource->code) }}"
                        class="form-control @error('code') is-invalid @enderror">
                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tipo</label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="internal" @selected(old('type', $fundingSource->type) === 'internal')>Interna</option>
                        <option value="external" @selected(old('type', $fundingSource->type) === 'external')>Externa</option>
                    </select>
                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Valor Total</label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="total_amount"
                        value="{{ old('total_amount', $fundingSource->total_amount) }}"
                        class="form-control @error('total_amount') is-invalid @enderror">
                    @error('total_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <div class="form-check">
                        <input type="hidden" name="active" value="0">
                        <input
                            type="checkbox"
                            name="active"
                            value="1"
                            id="active"
                            class="form-check-input"
                            @checked((bool) old('active', $fundingSource->active ?? true))>
                        <label for="active" class="form-check-label">Ativa</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Início</label>
                    <input type="date" name="start_date" value="{{ old('start_date', optional($fundingSource->start_date)->format('Y-m-d')) }}" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Fim</label>
                    <input type="date" name="end_date" value="{{ old('end_date', optional($fundingSource->end_date)->format('Y-m-d')) }}" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $fundingSource->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Contato</label>
                <textarea name="contact_info" class="form-control" rows="2">{{ old('contact_info', $fundingSource->contact_info) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Endereço</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $fundingSource->address) }}</textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.funding-sources.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

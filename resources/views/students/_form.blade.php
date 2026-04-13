<div class="row g-3">

    <div class="col-md-6">
        <label>Nome</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $student->name ?? '') }}">
    </div>

    <div class="col-md-6">
        <label>Turma</label>
        <select name="class_offering_id" class="form-select">
            @foreach($classes as $c)
                <option value="{{ $c->id }}"
                    @selected(old('class_offering_id', $student->class_offering_id ?? '') == $c->id)>
                    {{ $c->name ?? 'Turma '.$c->id }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label>CPF</label>
        <input type="text" name="cpf" class="form-control"
               value="{{ old('cpf', $student->cpf ?? '') }}">
    </div>

    <div class="col-md-4">
        <label>Passaporte</label>
        <input type="text" name="passport" class="form-control"
               value="{{ old('passport', $student->passport ?? '') }}">
    </div>

    <div class="col-md-4">
        <label>Tipo pagamento</label>
        <select name="payment_type" class="form-select">
            <option value="pix">PIX</option>
            <option value="transfer">Transferência</option>
        </select>
    </div>

</div>
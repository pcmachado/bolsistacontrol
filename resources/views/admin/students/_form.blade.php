<div class="row g-3">
    @if ($errors->any())
        <div class="col-12">
            <div class="alert alert-danger mb-0">
                <div class="fw-semibold">Não foi possível salvar o aluno.</div>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="col-md-6">
        <label>Nome</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $student->name ?? '') }}">
    </div>

    <div class="col-md-6">
        <label>Turma</label>
        @php
            $currentClassId = old(
                'class_offering_id',
                $selectedClasses[0] ?? null
            );
        @endphp
        <select name="class_offering_id" class="form-select">
            <option value="">Selecione</option>
            @foreach($classes as $c)
                <option value="{{ $c->id }}"
                    @selected((string) $currentClassId === (string) $c->id)>
                    {{ $c->name ?? 'Turma '.$c->id }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label>CPF</label>
        <input type="text" name="cpf" class="form-control"
               value="{{ old('cpf', $student->cpf ?? '') }}">
    </div>

    <div class="col-md-3">
        <label>Passaporte</label>
        <input type="text" name="passport" class="form-control"
               value="{{ old('passport', $student->passport ?? '') }}">
    </div>

    <div class="col-md-3">
        <label>E-mail</label>
        <input type="email" name="email" class="form-control"
               value="{{ old('email', $student->email ?? '') }}">
    </div>

    <div class="col-md-3">
        <label>Telefone</label>
        <input type="text" name="phone" class="form-control"
               value="{{ old('phone', $student->phone ?? '') }}">
    </div>

    <div class="col-md-4">
        <label>Tipo pagamento</label>
        <select name="payment_type" id="payment_type" class="form-select">
            <option value="">Selecione</option>
            <option value="pix" @selected(old('payment_type', $student->payment_type ?? '') == 'pix')>PIX</option>
            <option value="transfer" @selected(old('payment_type', $student->payment_type ?? '') == 'transfer')>Transferência</option>
        </select>
    </div>

    {{-- PIX --}}
    <div id="pix_fields" class="col-md-4 d-none">
        <label>Chave PIX</label>
        <input type="text" name="pix_key" class="form-control"
            value="{{ old('pix_key', $student->pix_key ?? '') }}">
    </div>

    {{-- TRANSFERÊNCIA --}}
    <div id="bank_fields" class="row g-3 d-none">

        <div class="col-md-4">
            <label>Banco</label>
            <input type="text" name="bank" class="form-control"
                value="{{ old('bank', $student->bank ?? '') }}">
        </div>

        <div class="col-md-4">
            <label>Agência</label>
            <input type="text" name="agency" class="form-control"
                value="{{ old('agency', $student->agency ?? '') }}">
        </div>

        <div class="col-md-4">
            <label>Conta</label>
            <input type="text" name="account" class="form-control"
                value="{{ old('account', $student->account ?? '') }}">
        </div>

    </div>

</div>

<script>
    function togglePaymentFields() {
        const type = document.getElementById('payment_type').value;

        const pix = document.getElementById('pix_fields');
        const bank = document.getElementById('bank_fields');

        pix.classList.add('d-none');
        bank.classList.add('d-none');

        if (type === 'pix') {
            pix.classList.remove('d-none');
        }

        if (type === 'transfer') {
            bank.classList.remove('d-none');
        }
    }

    document.getElementById('payment_type').addEventListener('change', togglePaymentFields);

    // 🔥 carregar corretamente ao abrir edição
    window.addEventListener('load', togglePaymentFields);
</script>

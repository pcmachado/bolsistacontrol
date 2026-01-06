<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-start border-4 border-secondary">
            <div class="card-body">
                <small>Gerados</small>
                <h3 id="card-fin-generated">0</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-start border-4 border-warning">
            <div class="card-body">
                <small>Pagos</small>
                <h3 id="card-fin-paid">0</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-start border-4 border-success">
            <div class="card-body">
                <small>Confirmados</small>
                <h3 id="card-fin-confirmed">0</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-start border-4 border-primary">
            <div class="card-body">
                <small>Total (R$)</small>
                <h3 id="card-fin-total">0,00</h3>
            </div>
        </div>
    </div>
</div>

<section class="row g-4 mb-4">
    {{-- Gráfico --}}
    <div class="col-lg-8">
        <div class="card shadow-sm rounded-0 border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0 text-muted">
                        Distribuição dos Registros Financeiros
                        @if($unitName)
                            — <span class="fw-normal">{{ $unitName }}</span>
                        @endif
                    </h5>
                    <div>
                        <button id="toggleChart" type="button" class="btn btn-sm btn-outline-secondary rounded-0">
                            Alternar Gráfico
                        </button>
                    </div>
                </div>
                <canvas id="financialChart" style="max-height: 220px;"></canvas>
            </div>
        </div>
    </div>
</section>
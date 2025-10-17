@extends('layouts.app')

<style>
    .nav-pills .nav-link.completed {
        background-color: #198754; /* verde */
        color: #fff;
    }
</style>

@section('content')
    <div class="container">
        <h3>Passo 3: Vincular Bolsistas</h3>
        @include('admin.projects.partials._steps', ['step' => 3, 'project' => $project ?? null])
        @include('admin.projects.partials._progress', ['progress' => 48, 'label' => 'Passo 3 de 6'])
        <p>Adicione os bolsistas que participarão deste projeto.</p>

        {{-- Campo de busca --}}
        <div class="mb-3">
            <label for="searchScholarshipHolder">Buscar bolsista (nome ou CPF)</label>
            <input type="text" id="searchScholarshipHolder" class="form-control" placeholder="Digite para buscar...">
            <div id="searchResults" class="list-group mt-2"></div>
        </div>

        {{-- Modal para vínculo --}}
        <div class="modal fade" id="addScholarshipModal" tabindex="-1">
            <div class="modal-dialog">
                <form id="addScholarshipForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Adicionar Bolsista</h5>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="scholarship_holder_id" id="scholarshipHolderId">

                            <div class="mb-2">
                                <label>Cargo</label>
                                <select name="position_id" class="form-select">
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label>Carga Horária</label>
                                <input type="number" name="weekly_workload" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label>Valor da Bolsa</label>
                                <input type="number" step="0.01" name="hourly_rate" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label>Atribuições</label>
                                <input type="text" step="0.01" name="assignments" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="Active">Ativo</option>
                                    <option value="Inactive">Inativo</option>
                                    <option value="Completed">Concluído</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label>Início</label>
                                <input type="date" name="start_date" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label>Fim</label>
                                <input type="date" name="end_date" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Confirmar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Listagem dos vínculos adicionados --}}
    <form method="POST" action="{{ route('admin.projects.store.step3', $project) }}">
        @csrf

        <table class="table mt-4" id="scholarshipList">
            <thead>
                <tr>
                <th>Bolsista</th>
                <th>Cargo</th>
                <th>Carga Horária</th>
                <th>Valor</th>
                <th>Período</th>
                <th>Situação</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <button class="btn btn-primary" id="prevStep">Passo Anterior</button>
        <button class="btn btn-success" id="nextStep">Finalizar</button>
    </form>
@endsection


<script>
    const searchUrl = "{{ route('scholarshipholders.search') }}";

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchScholarshipHolder');
        const resultsDiv = document.getElementById('searchResults');
        const listTable = document.querySelector('#scholarshipList tbody');

        let selectedHolder = null;

        searchInput.addEventListener('input', function() {
            fetch(`${searchUrl}?q=${this.value}`)
                .then(res => res.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    data.forEach(holder => {
                        const item = document.createElement('button');
                        item.classList.add('list-group-item', 'list-group-item-action');
                        item.textContent = `${holder.name} (${holder.cpf})`;
                        item.onclick = () => {
                            selectedHolder = holder;
                            document.getElementById('scholarshipHolderId').value = holder.id;
                            new bootstrap.Modal(document.getElementById('addScholarshipModal')).show();
                        };
                        resultsDiv.appendChild(item);
                    });
                });
        });

        document.getElementById('addScholarshipForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            // Adiciona na tabela abaixo
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${selectedHolder.name}</td>
                <td>${formData.get('position_id')}</td>
                <td>${formData.get('weekly_workload')}</td>
                <td>${formData.get('hourly_rate')}</td>
                <td>${formData.get('assignments')}</td>
                <td>${formData.get('status') == 'Active' ? 'Ativo' : formData.get('status') == 'Inactive' ? 'Inativo' : 'Concluído'}</td>
                <td>${formData.get('start_date')} - ${formData.get('end_date')}</td>
            `;

            // Campos hidden para enviar no POST final
            row.innerHTML += `
                <input type="hidden" name="scholarships[${Date.now()}][scholarship_holder_id]" value="${formData.get('scholarship_holder_id')}">
                <input type="hidden" name="scholarships[${Date.now()}][position_id]" value="${formData.get('position_id')}">
                <input type="hidden" name="scholarships[${Date.now()}][weekly_workload]" value="${formData.get('weekly_workload')}">
                <input type="hidden" name="scholarships[${Date.now()}][hourly_rate]" value="${formData.get('hourly_rate')}">
                <input type="hidden" name="scholarships[${Date.now()}][assignments]" value="${formData.get('assignments')}">
                <input type="hidden" name="scholarships[${Date.now()}][status]" value="${formData.get('status')}">
                <input type="hidden" name="scholarships[${Date.now()}][start_date]" value="${formData.get('start_date')}">
                <input type="hidden" name="scholarships[${Date.now()}][end_date]" value="${formData.get('end_date')}">
            `;

            listTable.appendChild(row);

            bootstrap.Modal.getInstance(document.getElementById('addScholarshipModal')).hide();
            this.reset();
        });
    });
</script>
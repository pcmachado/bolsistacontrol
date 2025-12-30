<div class="d-flex justify-content-center gap-2">
    {{-- Botão Visualizar --}}
    @if($model->status === 'draft' && $model->wizard_step)

        {{-- STEP 1 (edição especial) --}}
        @if($model->wizard_step === 'step1')
            <a href="{{ route('admin.projects.edit.step1', $model) }}"
            class="btn btn-sm btn-warning rounded-0"
            title="Continuar cadastro">
                <i class="bi bi-caret-right"></i>
            </a>

        {{-- REVIEW --}}
        @elseif($model->wizard_step === 'review')
            <a href="{{ route('admin.projects.review', $model) }}"
            class="btn btn-sm btn-warning rounded-0"
            title="Revisar projeto">
                <i class="bi bi-clipboard-check"></i>
            </a>

        {{-- DEMAIS STEPS --}}
        @else
            <a href="{{ route('admin.projects.create.' . $model->wizard_step, $model) }}"
            class="btn btn-sm btn-warning rounded-0"
            title="Continuar cadastro">
                <i class="bi bi-caret-right"></i>
            </a>
        @endif

    @else
        <a href="{{ route('admin.projects.show', $model) }}"
        class="btn btn-sm btn-outline-secondary rounded-0"
        title="Visualizar projeto">
            <i class="bi bi-eye"></i>
        </a>
    @endif

    {{-- Botão Editar --}}
    <a href="{{ route('admin.projects.edit.index', $id) }}" 
       class="btn btn-sm btn-primary rounded-0" 
       title="Editar">
        <i class="bi bi-pencil-square"></i>
    </a>

    {{-- Botão Excluir --}}
    <form action="{{ route('admin.projects.destroy', $id) }}" method="POST" 
          onsubmit="return confirm('Tem certeza que deseja excluir?');" 
          style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger rounded-0" title="Excluir">
            <i class="bi bi-trash"></i>
        </button>
    </form>
</div>
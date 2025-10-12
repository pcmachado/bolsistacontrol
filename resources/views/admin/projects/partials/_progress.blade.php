{{-- resources/views/projects/wizard/_progress.blade.php --}}
<div class="progress mb-4" style="height: 25px;">
    <div class="progress-bar bg-primary" role="progressbar" 
         style="width: {{ $progress }}%;" 
         aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
        {{ $label }}
    </div>
</div>
{{-- Usage example in a step view --}}
{{-- @include('admin.projects.wizard._progress', ['progress' => 75, 'label' => 'Passo 3 de 4']) --}}
@extends('layouts.app')

@section('content')

<div class="container-fluid px-4">

    {{-- 🔄 Barra de progresso do Wizard --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body py-3">

            @php
                $steps = [
                    'step1' => 'Projeto',
                    'step2' => 'Cargos',
                    'step3' => 'Cursos',
                    'step4' => 'Bolsistas',
                    'step5' => 'Fomento',
                    'review' => 'Revisão',
                ];

                $current = $project->wizard_step ?? 'step1';
                $keys = array_keys($steps);
                $currentIndex = array_search($current, $keys);
            @endphp

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                @foreach($steps as $key => $label)
                    @php
                        $index = array_search($key, $keys);
                        $state =
                            $index < $currentIndex ? 'completed' :
                            ($index === $currentIndex ? 'current' : 'pending');
                    @endphp

                    <div class="d-flex align-items-center gap-2">
                        <div class="
                            rounded-circle d-flex align-items-center justify-content-center
                            {{ $state === 'completed' ? 'bg-success text-white' : '' }}
                            {{ $state === 'current' ? 'bg-primary text-white' : '' }}
                            {{ $state === 'pending' ? 'bg-light border text-muted' : '' }}
                        "
                        style="width:32px;height:32px;font-size:0.85rem;">
                            {{ $index + 1 }}
                        </div>
                        <span class="
                            small fw-semibold
                            {{ $state === 'pending' ? 'text-muted' : '' }}
                        ">
                            {{ $label }}
                        </span>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    {{-- Conteúdo do passo --}}
    <div class="card shadow-sm">
        <div class="card-body p-4">
            @yield('wizard-content')
        </div>
    </div>

</div>

@endsection

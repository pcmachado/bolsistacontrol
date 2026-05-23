@extends('layouts.guest')

@section('title', 'Sessão Expirada')

@section('content')

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-6">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-5 text-center">

                    <div class="mb-4">
                        <i class="bi bi-shield-exclamation text-warning"
                           style="font-size: 4rem;"></i>
                    </div>

                    <h3 class="fw-bold mb-3">
                        Sessão expirada
                    </h3>

                    <p class="text-muted mb-4">

                        Sua sessão foi encerrada ou atualizada
                        devido a um novo acesso ao sistema.

                    </p>

                    <p class="small text-muted mb-4">

                        Isso pode acontecer após:
                        <br>
                        • login em outro navegador
                        <br>
                        • autenticação institucional IFRS
                        <br>
                        • tempo prolongado de inatividade

                    </p>

                    <a href="{{ route('login') }}"
                       class="btn btn-primary px-4">

                        Voltar ao login

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
@extends('layouts.guest')

@section('title', 'Bem-vindo')

@section('content')

<section class="py-5" style="background:linear-gradient(135deg, #032454 0%, #0b376f 100%); min-height: 75vh;">

    <div class="container">

        <div class="row align-items-center justify-content-center">

            <div class="col-lg-10">

                <div class="bg-white shadow-lg rounded-4 overflow-hidden">

                    <div class="row g-0">

                        {{-- Área institucional --}}
                        <div class="col-lg-6 d-flex flex-column justify-content-center p-5 rounded-0">
                            <img src="{{ asset('images/probolsas_fundo_escuro.png') }}" alt="ProBolsas" class="img-fluid mb-4" style="max-width: 420px;">

                            {{-- <h2 class="fw-bold mb-3" style="color:#032454;">Gestão Acadêmica Moderna</h2> --}}

                            <p class="lead text-muted mb-4">Plataforma para Gestão Integrada de Bolsistas, Frequências e Pagamentos.</p>

                            <div class="d-flex flex-wrap gap-3 rounded-0">

                                <a href="{{ route('login') }}" class="btn btn-primary btn-lg rounded-0 px-4" style="background:#032454; border:none;" >Entrar no Sistema</a>

                                <a href="{{ route('payments.verify.form') }}" class="btn btn-outline-secondary btn-lg rounded-0 px-4">Validar Recibo</a>

                            </div>

                        </div>

                        {{-- Área destaque --}}
                        <div
                            class="col-lg-6 p-5 text-white"
                            style="
                                background:
                                linear-gradient(
                                    180deg,
                                    #032454 0%,
                                    #0d4a92 100%
                                );
                            "
                        >

                            <h3 class="fw-bold mb-4">
                                Recursos da Plataforma
                            </h3>

                            <div class="mb-4">
                                <h5>📚 Programas, Cursos e Turmas</h5>
                                <p class="opacity-75">
                                    Gerencie programas de cursos FIC de forma centralizada.
                                </p>
                            </div>

                            <div class="mb-4">
                                <h5>💰 Bolsas e Pagamentos</h5>
                                <p class="opacity-75">
                                    Controle administrativo de bolsas, recibos e pagamentos.
                                </p>
                            </div>

                            <div class="mb-4">
                                <h5>📋 Frequências</h5>
                                <p class="opacity-75">
                                    Registro e validação de frequências para apoio à execução e prestação de contas.
                                </p>
                            </div>

                            <div>
                                <h5>🔐 Gestão Institucional</h5>
                                <p class="opacity-75">
                                    Permissões, unidades e acompanhamento operacional.
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection
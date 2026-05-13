@extends('layouts.guest')

@section('title', 'Verificação de E-mail')

@section('content')

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card shadow-sm border-0">

                <div class="card-body p-5">

                    <div class="text-center mb-4">

                        <div class="mb-3">
                            <i class="bi bi-envelope-check display-3 text-primary"></i>
                        </div>

                        <h3 class="fw-bold">
                            Verifique seu e-mail
                        </h3>

                        <p class="text-muted mt-3">
                            Antes de continuar, confirme seu endereço de e-mail
                            clicando no link enviado para sua caixa de entrada.
                        </p>

                    </div>

                    @if (session('status') == 'verification-link-sent')

                        <div class="alert alert-success">

                            Um novo link de verificação foi enviado para seu e-mail.

                        </div>

                    @endif

                    <div class="d-grid gap-3">

                        <form method="POST"
                              action="{{ route('verification.send') }}">

                            @csrf

                            <button type="submit"
                                    class="btn btn-primary w-100">

                                Reenviar e-mail de verificação

                            </button>

                        </form>

                        <form method="POST"
                              action="{{ route('logout') }}">

                            @csrf

                            <button type="submit"
                                    class="btn btn-outline-secondary w-100">

                                Sair do sistema

                            </button>

                        </form>

                    </div>

                    <hr class="my-4">

                    <div class="small text-muted text-center">

                        Caso não encontre o e-mail, verifique sua caixa de spam
                        ou lixo eletrônico.

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
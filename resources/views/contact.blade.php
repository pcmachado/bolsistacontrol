@extends('layouts.guest')

@section('title', 'Contato')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="mb-4">Entre em Contato</h2>
        <p class="text-muted">Use o formulário abaixo para enviar sua mensagem à equipe de suporte.</p>

        <form method="POST" action="{{ route('contact.send') }}">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="message" class="form-label">Mensagem</label>
                <textarea name="message" id="message" rows="5" class="form-control" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    </div>
</div>
@endsection

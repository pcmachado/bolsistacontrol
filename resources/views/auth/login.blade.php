@extends('layouts.app')

@section('title','Login')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height:75vh;">
  <div class="card w-100" style="max-width:420px;">
    <div class="card-body">
      <h4 class="card-title mb-3">Login</h4>

      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('admin') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label">Senha</label>
          <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3 form-check">
          <input type="checkbox" name="remember" id="remember" class="form-check-input">
          <label for="remember" class="form-check-label">Lembrar-me</label>
        </div>

        <button class="btn btn-primary w-100" type="submit">Entrar</button>
      </form>
    </div>
  </div>
</div>
@endsection

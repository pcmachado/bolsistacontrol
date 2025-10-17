<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo(Request $request): ?string
    {
        // Se o pedido NÃO for uma chamada AJAX (ou seja, não espera JSON),
        // redireciona o utilizador para a rota de login.
        // Se FOR uma chamada AJAX, retorna null. Isto faz com que o Laravel
        // lance uma AuthenticationException, que por sua vez resulta num erro 401
        // em formato JSON, que o DataTables entende corretamente.
        return $request->expectsJson() ? null : route('login');
    }
}
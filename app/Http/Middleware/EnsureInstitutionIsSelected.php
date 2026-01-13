<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Institution;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstitutionIsSelected
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Ignora rotas públicas
        if (! $user || $request->routeIs(['institution.select', 'institution.set', 'logout'])) {
            return $next($request);
        }

        // Admin → define automaticamente a primeira instituição
        if ($user->isAdmin()) {
            if (!session()->has('institution_id')) {
                session(['institution_id' => Institution::value('id')]);
            }
            return $next($request);
        }

        $institutions = $user->institutions()->select('institutions.id', 'institutions.name')->get();

        // Nenhum vínculo
        if ($institutions->isEmpty()) {
            abort(403, 'Você não possui nenhuma instituição vinculada.');
        }

        // Apenas uma → define automaticamente
        if ($institutions->count() === 1) {
            session(['institution_id' => $institutions->first()->id]);
            return $next($request);
        }

        // Múltiplas → exige escolha
        if (!session()->has('institution_id')) {
            return redirect()->route('institution.select');
        }

        return $next($request);
    }
}

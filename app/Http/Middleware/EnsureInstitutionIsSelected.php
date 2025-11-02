<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Institution;

class EnsureInstitutionIsSelected
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Admin → pode ver todas
        if ($user->isAdmin()) {
            if (!session()->has('institution_id')) {
                session(['institution_id' => Institution::first()?->id]);
            }
            return $next($request);
        }

        $institutions = $user->institutions;

        if ($institutions->count() === 1) {
            // único vínculo → seta automaticamente
            session(['institution_id' => $institutions->first()->id]);
        } elseif (!session()->has('institution_id')) {
            // múltiplos vínculos → redireciona para seleção
            return redirect()->route('institution.select');
        }

        return $next($request);
    }
}

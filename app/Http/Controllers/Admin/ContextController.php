<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContextController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'institution_id' => 'nullable|exists:institutions,id',
        ]);

        $user = Auth::user();

        if ($request->filled('institution_id')) {
            $institutionId = (int) $request->institution_id;

            abort_unless($user->canAccessInstitution($institutionId), 403);

            session(['admin_institution_context' => $institutionId]);

            $institution = Institution::findOrFail($institutionId);
            $message = "Visualizando dados da instituicao: {$institution->name}";
        } else {
            session()->forget('admin_institution_context');
            $message = 'Visualizando dados de todas as instituicoes.';
        }

        return back()->with('success', $message);
    }
}

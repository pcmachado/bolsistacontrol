<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardResolverController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        // if ($user->hasAnyRole(['coordenador_geral', 'coordenador_adjunto_geral', 'coordenador_adjunto'])) {
        //     return redirect()->route('admin.dashboard'); // operacional
        // }

        // if ($user->hasRole('professor')) {
        //     return redirect()->route('teacher.dashboard');
        // }

        return redirect()->route('holder.dashboard');
    }
}

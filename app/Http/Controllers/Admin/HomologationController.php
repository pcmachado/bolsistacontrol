<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class HomologationController extends Controller
{
    /**
     * Exibe a listagem de registros pendentes para homologação.
     */
    public function index(): View
    {
        $records = AttendanceRecord::where('status', 'pendente')->get();
        return view('admin.homologation.index', compact('records'));
    }

    /**
     * Homologa um registro.
     */
    public function homologar(AttendanceRecord $record): RedirectResponse
    {
        $record->update([
            'status' => 'homologado_adjunto',
            'approved_by_id' => Auth::id()
        ]);

        return back()->with('success', 'Registro homologado com sucesso!');
    }

    /**
     * Rejeita um registro.
     */
    public function rejeitar(AttendanceRecord $record): RedirectResponse
    {
        $record->update([
            'status' => 'rejeitado'
        ]);

        return back()->with('error', 'Registro rejeitado.');
    }
}
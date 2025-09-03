<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipHolder;
use App\Models\Position;
use App\Models\Notification;
use App\Models\AttendanceRecord;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ScholarshipHolderController extends Controller
{
    public function index(): View
    {
        $bolsistas = ScholarshipHolder::with(/*'role',*/ 'units')->paginate(15);
        return view('scholarship-holders.index', compact('bolsistas'));
    }

    public function create(): View
    {
        $unidades = Unit::all();
        $roles = Role::all();
        return view('scholarship-holders.create', compact('unidades', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|unique:scholarship_holders,cpf',
            'email' => 'required|email|unique:scholarship_holders,email',
            'role_id' => 'required|exists:roles,id',
            'unit_id' => 'required|exists:units,id',
            'monthly_workload' => 'required|integer|min:1',
            // Validações para dados bancários
            'bank' => 'nullable|string',
            'agency' => 'nullable|string',
            'account' => 'nullable|string',
        ]);

        // Cria um usuário para o bolsista (com senha padrão)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'), // Senha padrão para o primeiro acesso
            'role' => 'bolsista',
        ]);

        $scholarshipHolder = ScholarshipHolder::create($request->all());

        $scholarshipHolder->units()->attach($request->unit_id, [
            'monthly_workload' => $request->monthly_workload,
            'start_date' => now()->toDateString()
        ]);

        return redirect()->route('scholarship-holders.index')->with('success', 'Bolsista cadastrado com sucesso!');
    }

    /**
     * Exibe o formulário para editar um bolsista.
     */
    public function edit(ScholarshipHolder $bolsista): View
    {
        $unidades = Unit::all();
        $cargos = Position::all();
        $unidadeAtual = $bolsista->units()->first();
        return view('scholarship-holders.edit', compact('bolsista', 'unidades', 'cargos', 'unidadeAtual'));
    }

    /**
     * Atualiza um bolsista no banco de dados.
     */
    public function update(Request $request, ScholarshipHolder $bolsista): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => ['required', 'string', Rule::unique('scholarship_holders')->ignore($bolsista->id)],
            'email' => ['required', 'email', Rule::unique('scholarship_holders')->ignore($bolsista->id)],
            // ... outras validações
        ]);
  
        $bolsista->update($request->all());

        // Atualiza a unidade e carga horária se necessário
        $bolsista->units()->sync([$request->unidade_id => ['monthly_workload' => $request->carga_horaria]]);

        return redirect()->route('scholarship-holders.index')->with('success', 'Bolsista atualizado com sucesso!');
    }

    /**
     * Remove um bolsista do banco de dados.
     */
    public function destroy(ScholarshipHolder $bolsista): RedirectResponse
    {
        // Deleta o usuário associado para evitar dados órfãos
        $bolsista->user()->delete();
        $bolsista->delete();

        return redirect()->route('scholarship-holders.index')->with('success', 'Bolsista removido com sucesso!');
    }
}

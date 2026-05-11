<?php

namespace App\Http\Controllers;

use App\DataTables\ScholarshipHoldersDataTable;
use App\Models\Institution;
use App\Models\ScholarshipHolder;
use App\Models\Unit;
use App\Models\User;
use App\Services\ScholarshipHolderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ScholarshipHolderController extends Controller
{
    protected ScholarshipHolderService $scholarshipHolderService;

    public function __construct(ScholarshipHolderService $scholarshipHolderService)
    {
        $this->scholarshipHolderService = $scholarshipHolderService;
    }

    public function index(ScholarshipHoldersDataTable $dataTable)
    {
        return $dataTable->render('admin.scholarship_holders.index');
    }

    public function create(): View
    {
        $unidades = Unit::all();
        $users = User::all();
        $institutions = Institution::all();

        return view('admin.scholarship_holders.create', compact('unidades', 'users', 'institutions'));
    }

    public function store(Request $request)
    {
        // 1. Validação
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|unique:scholarship_holders,cpf|max:14',
            'email' => 'required|email|unique:scholarship_holders,email|unique:users,email',
            'unit_id' => 'required|exists:units,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'position' => 'required|string|max:50',
            'phone' => 'nullable|string|max:15',
            'institution_link' => 'nullable|url',
            'bank' => 'nullable|string',
            'agency' => 'nullable|string',
            'account' => 'nullable|string',
            'pix_key' => 'nullable|string',
        ]);

        // Cria um usuário para o bolsista (com senha padrão)
        // Inicia a transação para garantir que ambos, Usuário e Bolsista, sejam criados ou nenhum seja.
        DB::beginTransaction();

        try {
            // 2. Cria ou Encontra o Usuário
            // Tenta encontrar um usuário pelo email (caso já exista uma conta)
            $user = User::firstWhere('email', $validatedData['email']);

            if (! $user) {
                // Cria um novo usuário, necessário para login.
                $user = User::create([
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'password' => Hash::make(/* $validatedData['cpf'] */ 'password'), // Senha inicial é o CPF
                ])->assignRole('bolsista');
            }

            // 3. Cria o Bolsista e o associa ao novo Usuário
            $scholarshipHolderData = array_merge($validatedData, [
                'user_id' => $user->id,
                // O Model cuida da criptografia dos dados bancários
            ]);

            $scholarshipHolder = $this->scholarshipHolderService->create($scholarshipHolderData);

            // Confirma a transação
            DB::commit();

            return redirect()->route('admin.scholarship_holders.index')->with('success', 'Bolsista e Usuário associado cadastrados com sucesso!');

        } catch (\Exception $e) {
            // Reverte a transação em caso de erro
            DB::rollBack();

            // Log do erro ($e->getMessage())
            return back()->withInput()->with('error', 'Erro ao cadastrar bolsista e usuário: '.$e->getMessage());
        }
    }

    /**
     * Exibe o formulário para editar um bolsista.
     */
    public function edit(ScholarshipHolder $scholarshipHolder): View
    {
        $unidades = Unit::all();
        $unidadeAtual = $scholarshipHolder->units()->first();

        return view('admin.scholarship_holders.edit', compact('bolsista', 'unidades', 'unidadeAtual'));
    }

    public function show(ScholarshipHolder $scholarshipHolder): View
    {
        $scholarshipHolder->load('unit', 'user');

        return view('admin.scholarship_holders.details', compact('scholarshipHolder'));
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

        return redirect()->route('admin.scholarship_holders.index')->with('success', 'Bolsista atualizado com sucesso!');
    }

    /**
     * Remove um bolsista do banco de dados.
     */
    public function destroy(ScholarshipHolder $bolsista): RedirectResponse
    {
        // Deleta o usuário associado para evitar dados órfãos
        $bolsista->user()->delete();
        $bolsista->delete();

        return redirect()->route('admin.scholarship_holders.index')->with('success', 'Bolsista removido com sucesso!');
    }

    public function search(Request $request)
    {
        $term = $request->get('q');

        $query = ScholarshipHolder::query()
            ->with('user');

        // 🔒 visibilidade institucional
        $query = app(\App\Services\VisibilityService::class)
            ->apply($query, auth()->user(), 'admin');

        // 🔎 busca
        if ($term) {
            $query->whereHas('user', function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            })->orWhere('cpf', 'like', "%{$term}%");
        }

        $holders = $query
            ->where('status', 'active')
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $holders->map(fn ($holder) => [
                'id' => $holder->id,
                'text' => $holder->user->name,
            ]),
        ]);
    }
}

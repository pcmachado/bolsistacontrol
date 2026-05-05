<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Institution;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmailTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $query = EmailTemplate::query()
            ->with(['project', 'institution']);

        // Filtrar por instituição se não for super admin
        if (! $user->hasRole('super-admin')) {
            $query->where(function ($q) use ($user) {
                $q->whereIn('institution_id', $user->accessibleInstitutionIds())
                    ->orWhereNull('institution_id');
            });
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        $templates = $query->paginate(20);

        $projects = Project::query()
            ->when(! $user->hasRole('super-admin'), function ($q) use ($user) {
                $q->whereIn('institution_id', $user->accessibleInstitutionIds());
            })
            ->orderBy('name')
            ->get();

        $institutions = Institution::query()
            ->when(! $user->hasRole('super-admin'), function ($q) use ($user) {
                $q->whereIn('id', $user->accessibleInstitutionIds());
            })
            ->orderBy('name')
            ->get();

        return view('email-templates.index', compact('templates', 'projects', 'institutions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = Auth::user();

        $projects = Project::query()
            ->when(! $user->hasRole('super-admin'), function ($q) use ($user) {
                $q->whereIn('institution_id', $user->accessibleInstitutionIds());
            })
            ->orderBy('name')
            ->get();

        $institutions = Institution::query()
            ->when(! $user->hasRole('super-admin'), function ($q) use ($user) {
                $q->whereIn('id', $user->accessibleInstitutionIds());
            })
            ->orderBy('name')
            ->get();

        return view('email-templates.create', compact('projects', 'institutions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'key' => 'required|string|unique:email_templates,key',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'body_text' => 'nullable|string',
            'variables' => 'nullable|json',
            'project_id' => 'nullable|exists:projects,id',
            'institution_id' => 'nullable|exists:institutions,id',
            'active' => 'boolean',
        ]);

        $user = Auth::user();

        // Verificar permissões
        if ($request->project_id) {
            $project = Project::find($request->project_id);
            if (! $user->hasRole('super-admin') && ! in_array($project->institution_id, $user->accessibleInstitutionIds())) {
                abort(403);
            }
        }

        if ($request->institution_id && ! $user->hasRole('super-admin') && ! in_array($request->institution_id, $user->accessibleInstitutionIds())) {
            abort(403);
        }

        EmailTemplate::create($request->all());

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template de email criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailTemplate $emailTemplate): View
    {
        $this->authorize('view', $emailTemplate);

        return view('email-templates.show', compact('emailTemplate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailTemplate $emailTemplate): View
    {
        $this->authorize('update', $emailTemplate);

        $user = Auth::user();

        $projects = Project::query()
            ->when(! $user->canAccessAdministrative(), function ($q) use ($user) {
                $q->whereIn('institution_id', $user->accessibleInstitutionIds());
            })
            ->orderBy('name')
            ->get();

        $institutions = Institution::query()
            ->when(! $user->canAccessAdministrative(), function ($q) use ($user) {
                $q->whereIn('id', $user->accessibleInstitutionIds());
            })
            ->orderBy('name')
            ->get();

        return view('email-templates.edit', compact('emailTemplate', 'projects', 'institutions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        $this->authorize('update', $emailTemplate);

        $request->validate([
            'key' => 'required|string|unique:email_templates,key,'.$emailTemplate->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'body_text' => 'nullable|string',
            'variables' => 'nullable|json',
            'project_id' => 'nullable|exists:projects,id',
            'institution_id' => 'nullable|exists:institutions,id',
            'active' => 'boolean',
        ]);

        $user = Auth::user();

        // Verificar permissões
        if ($request->project_id) {
            $project = Project::find($request->project_id);
            if (! $user->hasRole('super-admin') && ! in_array($project->institution_id, $user->accessibleInstitutionIds())) {
                abort(403);
            }
        }

        if ($request->institution_id && ! $user->hasRole('super-admin') && ! in_array($request->institution_id, $user->accessibleInstitutionIds())) {
            abort(403);
        }

        $emailTemplate->update($request->all());

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template de email atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailTemplate $emailTemplate): RedirectResponse
    {
        $this->authorize('delete', $emailTemplate);

        $emailTemplate->delete();

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Template de email excluído com sucesso.');
    }
}

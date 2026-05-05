<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\NotificationSetting;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class NotificationSettingController extends Controller
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
        $user = Auth::user();

        $query = NotificationSetting::query()
            ->with(['project', 'institution']);

        // Filtrar por instituição se não for super admin
        if (! $user->canAccessAdministrative()) {
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

        $settings = $query->paginate(20);

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

        return view('notification-settings.index', compact('settings', 'projects', 'institutions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
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

        $roles = Role::all();
        $eventTypes = $this->getAvailableEventTypes();

        return view('notification-settings.create', compact('projects', 'institutions', 'roles', 'eventTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'event_type' => 'required|string',
            'notification_type' => 'required|in:database,mail,both',
            'recipients' => 'required|array',
            'project_id' => 'nullable|exists:projects,id',
            'institution_id' => 'nullable|exists:institutions,id',
            'enabled' => 'boolean',
        ]);

        $user = Auth::user();

        // Verificar permissões
        if ($request->project_id) {
            $project = Project::find($request->project_id);
            if (! $user->canAccessAdministrative() && ! in_array($project->institution_id, $user->accessibleInstitutionIds())) {
                abort(403);
            }
        }

        if ($request->institution_id && ! $user->canAccessAdministrative() && ! in_array($request->institution_id, $user->accessibleInstitutionIds())) {
            abort(403);
        }

        NotificationSetting::create($request->all());

        return redirect()->route('admin.notification-settings.index')
            ->with('success', 'Configuração de notificação criada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(NotificationSetting $notificationSetting): View
    {
        $this->authorize('view', $notificationSetting);

        return view('notification-settings.show', compact('notificationSetting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NotificationSetting $notificationSetting): View
    {
        $this->authorize('update', $notificationSetting);

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

        $roles = Role::all();
        $eventTypes = $this->getAvailableEventTypes();

        return view('notification-settings.edit', compact('notificationSetting', 'projects', 'institutions', 'roles', 'eventTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NotificationSetting $notificationSetting): RedirectResponse
    {
        $this->authorize('update', $notificationSetting);

        $request->validate([
            'event_type' => 'required|string',
            'notification_type' => 'required|in:database,mail,both',
            'recipients' => 'required|array',
            'project_id' => 'nullable|exists:projects,id',
            'institution_id' => 'nullable|exists:institutions,id',
            'enabled' => 'boolean',
        ]);

        $user = Auth::user();

        // Verificar permissões
        if ($request->project_id) {
            $project = Project::find($request->project_id);
            if (! $user->canAccessAdministrative() && ! in_array($project->institution_id, $user->accessibleInstitutionIds())) {
                abort(403);
            }
        }

        if ($request->institution_id && ! $user->canAccessAdministrative() && ! in_array($request->institution_id, $user->accessibleInstitutionIds())) {
            abort(403);
        }

        $notificationSetting->update($request->all());

        return redirect()->route('admin.notification-settings.index')
            ->with('success', 'Configuração de notificação atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NotificationSetting $notificationSetting): RedirectResponse
    {
        $this->authorize('delete', $notificationSetting);

        $notificationSetting->delete();

        return redirect()->route('admin.notification-settings.index')
            ->with('success', 'Configuração de notificação excluída com sucesso.');
    }

    /**
     * Retorna os tipos de evento disponíveis
     */
    private function getAvailableEventTypes(): array
    {
        return [
            'payment_status_changed' => 'Mudança de Status de Pagamento',
            'submission_submitted' => 'Submissão de Frequência Enviada',
            'submission_approved' => 'Submissão de Frequência Aprovada',
            'submission_rejected' => 'Submissão de Frequência Rejeitada',
        ];
    }
}

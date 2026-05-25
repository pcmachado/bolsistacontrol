<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function scholarshipHolder(): HasOne
    {
        return $this->hasOne(ScholarshipHolder::class, 'user_id');
    }

    public function classOfferingDisciplines(): HasMany
    {
        return $this->hasMany(ClassOfferingDiscipline::class, 'teacher_id');
    }

    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class, 'institution_user')
            ->withPivot('active')
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'email',
        'password',
        'unit_id',
        'institution_id',
        'remember_token',
        'last_seen_version',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE HELPERS (APENAS ACESSO GLOBAL)
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['admin', 'superadmin']);
    }

    public function isCoordenadorGeral(): bool
    {
        return $this->hasRole('coordenador_geral');
    }

    public function isCoordenadorAdjuntoGeral(): bool
    {
        return $this->hasRole('coordenador_adjunto_geral');
    }

    public function isCoordenadorAdjunto(): bool
    {
        return $this->hasRole('coordenador_adjunto');
    }

    public function isCoordenador(): bool
    {
        return $this->hasAnyRole([
            'coordenador_geral',
            'coordenador_adjunto_geral',
            'coordenador_adjunto',
        ]);
    }

    public function isBolsista(): bool
    {
        return $this->hasRole('bolsista');
    }

    public function isAdminOnly(): bool
    {
        return $this->isAdmin() && ! $this->scholarshipHolder;
    }

    /*
    |--------------------------------------------------------------------------
    | CONTEXTO (NOVA LÓGICA)
    |--------------------------------------------------------------------------
    */

    /**
     * Retorna o ID institucional correto
     */
    public function resolvedInstitutionId(): ?int
    {
        return $this->activeInstitutionId()
            ?? $this->institution_id
            ?? $this->unit?->institution_id
            ?? $this->scholarshipHolder?->unit?->institution_id;
    }

    public function activeInstitutionId(): ?int
    {
        $selectedId = session('admin_institution_context')
            ?? session('institution_id');

        if ($selectedId && $this->canAccessInstitution((int) $selectedId)) {
            return (int) $selectedId;
        }

        return $this->institution_id
            ?? $this->unit?->institution_id
            ?? $this->scholarshipHolder?->unit?->institution_id
            ?? $this->accessibleInstitutionIds()->first();
    }

    public function accessibleInstitutionIds(): Collection
    {
        $linkedIds = $this->relationLoaded('institutions')
            ? $this->institutions->pluck('id')
            : $this->institutions()->pluck('institutions.id');

        if ($linkedIds->isNotEmpty()) {
            return $linkedIds->filter()->unique()->values();
        }

        $fallbackIds = collect([
            $this->institution_id,
            $this->unit?->institution_id,
            $this->scholarshipHolder?->unit?->institution_id,
        ])->filter()->unique()->values();

        if ($fallbackIds->isNotEmpty()) {
            return $fallbackIds;
        }

        if ($this->hasRole('superadmin')) {
            return Institution::query()->pluck('id');
        }

        return collect();
    }

    public function activeInstitutionIds(): Collection
    {
        $activeId = $this->activeInstitutionId();

        if ($activeId) {
            return collect([$activeId]);
        }

        return $this->accessibleInstitutionIds();
    }

    public function visibleUnitIds(): Collection
    {
        if ($this->isUnitScoped()) {
            return collect([
                $this->unit_id,
                $this->scholarshipHolder?->unit_id,
                $this->assignments()
                    ->where('active', true)
                    ->whereNotNull('unit_id')
                    ->value('unit_id'),
            ])->filter()->unique()->values();
        }

        if ($this->isInstitutionScoped()) {
            $institutionIds = $this->activeInstitutionIds();

            if ($institutionIds->isEmpty()) {
                return collect();
            }

            return Unit::query()
                ->withoutGlobalScopes()
                ->whereIn('institution_id', $institutionIds)
                ->pluck('id');
        }

        $assignmentUnitIds = $this->assignments()
            ->where('active', true)
            ->whereNotNull('unit_id')
            ->pluck('unit_id')
            ->unique()
            ->values();

        if ($assignmentUnitIds->isNotEmpty()) {
            return $assignmentUnitIds;
        }

        return collect([$this->unit_id])->filter()->values();
    }

    public function visibleProjectIds(): Collection
    {
        $assignedProjectIds = $this->assignments()
            ->where('active', true)
            ->whereNotNull('project_id')
            ->pluck('project_id')
            ->unique()
            ->values();

        if ($assignedProjectIds->isNotEmpty()) {
            return $assignedProjectIds;
        }

        if ($this->isInstitutionScoped()) {
            $institutionIds = $this->activeInstitutionIds();

            if ($institutionIds->isEmpty()) {
                return collect();
            }

            return Project::query()
                ->withoutGlobalScopes()
                ->whereIn('institution_id', $institutionIds)
                ->pluck('id');
        }

        $unitIds = $this->visibleUnitIds();

        if ($unitIds->isEmpty()) {
            return collect();
        }

        return Project::query()
            ->withoutGlobalScopes()
            ->whereHas('classOfferings', function ($query) use ($unitIds) {
                $query->whereIn('unit_id', $unitIds);
            })
            ->pluck('projects.id')
            ->unique()
            ->values();
    }

    public function canAccessInstitution(int $institutionId): bool
    {
        return $this->accessibleInstitutionIds()->contains($institutionId);
    }

    public function isInstitutionScoped(): bool
    {
        return $this->hasAnyRole([
            'superadmin',
            'admin',
            'coordenador_geral',
            'coordenador_adjunto_geral',
        ]);
    }

    public function isUnitScoped(): bool
    {
        return $this->hasAnyRole([
            'coordenador_adjunto',
            'supervisor',
            'apoio_administrativo',
            'orientador',
            'bolsista',
        ]);
    }

    public function units()
    {
        return Unit::query()->whereIn('id', $this->visibleUnitIds());
    }

    /**
     * Verifica se o usuário está vinculado a uma turma (qualquer papel)
     */
    public function isLinkedToOffering(ClassOffering $offering): bool
    {
        if (! $this->scholarshipHolder) {
            return false;
        }

        return $offering->scholarshipHolders()
            ->where('scholarship_holder_id', $this->scholarshipHolder->id)
            ->exists();
    }

    /**
     * Verifica se o usuário atua como professor na turma
     */
    public function isProfessorInOffering(ClassOffering $offering): bool
    {
        return $offering->disciplines()
            ->where('teacher_id', $this->id)
            ->exists();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function hasAssignment(string $type): bool
    {
        return $this->assignments()
            ->where('assignment_type', $type)
            ->where('active', true)
            ->exists();
    }

    public function canAccessFinancial(): bool
    {
        return $this->hasRole('coordenador_geral')
            || $this->hasRole('coordenador_adjunto_geral');
    }

    public function canAccessAdministrative(): bool
    {
        return $this->hasAnyRole(['superadmin', 'admin'])
            || $this->hasRole('coordenador_geral')
            || $this->hasRole('coordenador_adjunto_geral');
    }

    public function canAccessTeacher(): bool
    {
        return $this->assignments()
            ->where('assignment_type', Assignment::TYPE_PROFESSOR)
            ->where('active', true)
            ->exists();
    }

    public function canAccessCoordination(): bool
    {
        return $this->hasRole('coordenador_adjunto')
            || $this->hasRole('coordenador_adjunto_geral')
            || $this->hasRole('coordenador_geral');
    }

    public function canAccessMy(): bool
    {
        return $this->hasAnyRole([
            'coordenador_geral',
            'coordenador_adjunto_geral',
            'coordenador_adjunto',
            'supervisor',
            'apoio_administrativo',
            'orientador',
        ]) || $this->isBolsista();
    }
}

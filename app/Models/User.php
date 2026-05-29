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
    | CONTEXTO INSTITUCIONAL SIMPLES
    |--------------------------------------------------------------------------
    */

    public function resolvedInstitutionId(): ?int
    {
        if ($this->institution_id) {
            return $this->institution_id;
        }

        if ($this->unit_id) {
            return Unit::query()
                ->withoutGlobalScopes()
                ->whereKey($this->unit_id)
                ->value('institution_id');
        }

        if ($this->scholarshipHolder?->unit_id) {
            return Unit::query()
                ->withoutGlobalScopes()
                ->whereKey($this->scholarshipHolder->unit_id)
                ->value('institution_id');
        }

        return null;
    }

    public function accessibleInstitutionIds(): Collection
    {
        return collect([$this->resolvedInstitutionId()])
            ->filter()
            ->unique()
            ->values();
    }

    public function visibleUnitIds(): Collection
    {
        if ($this->unit_id) {
            return collect([$this->unit_id]);
        }

        if ($this->scholarshipHolder?->unit_id) {
            return collect([$this->scholarshipHolder->unit_id]);
        }

        $institutionId = $this->resolvedInstitutionId();

        if (! $institutionId) {
            return collect();
        }

        return Unit::query()
            ->withoutGlobalScopes()
            ->where('institution_id', $institutionId)
            ->pluck('id');
    }

    public function visibleProjectIds(): Collection
    {
        $institutionId = $this->resolvedInstitutionId();

        if (! $institutionId) {
            return collect();
        }

        return Project::query()
            ->where('institution_id', $institutionId)
            ->pluck('id');
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

    public function activeInstitutionId(): ?int
    {
        return $this->resolvedInstitutionId();
    }

    public function activeInstitutionIds(): Collection
    {
        return $this->accessibleInstitutionIds();
    }

    public function canAccessInstitution(int $institutionId): bool
    {
        return $this->accessibleInstitutionIds()->contains($institutionId);
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
        if (! $this->scholarshipHolder) {
            return false;
        }

        return $offering->classOfferingDisciplines()
            ->where('teacher_id', $this->scholarshipHolder->id)
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
        if (! $this->scholarshipHolder) {
            return false;
        }

        return ProjectScholarshipHolder::query()
            ->where('scholarship_holder_id', $this->scholarshipHolder->id)
            ->whereHas('position', fn ($q) => $q->where('is_teacher', true))
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

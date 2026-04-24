<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

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
            'coordenador_adjunto'
        ]);
    }

    public function isBolsista(): bool
    {
        return $this->hasRole('bolsista');
    }

    public function isAdminOnly(): bool
    {
        return $this->isAdmin() && !$this->scholarshipHolder;
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
        return $this->unit?->institution_id ?? $this->institution_id;
    }

    /**
     * Verifica se o usuário está vinculado a uma turma (qualquer papel)
     */
    public function isLinkedToOffering(ClassOffering $offering): bool
    {
        if (!$this->scholarshipHolder) {
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
}
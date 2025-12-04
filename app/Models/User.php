<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    // Relacionamento: um usuário pertence a uma unidade
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function scholarshipHolder(): HasOne
    {
        return $this->hasOne(ScholarshipHolder::class, 'user_id');
    }

    public function institutions()
    {
        return $this->belongsToMany(Institution::class, 'institution_user')
            ->withPivot('active')
            ->withTimestamps();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'unit_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

        /**
     * Verifica se o usuário tem o papel de coordenador geral.
     */
    public function isCoordenadorGeral(): bool
    {
        return $this->hasRole('coordenador_geral');
    }

    /**
     * Verifica se o usuário tem o papel de administrador.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isCoordenadorAdjunto(): bool
    {
        return $this->hasRole('coordenador_adjunto');
    }

    public function isCoordenador(): bool
    {
        return $this->hasAnyRole(['coordenador_geral', 'coordenador_adjunto']);
    }

    public function activeInstitutions()
    {
        return $this->institutions()->wherePivot('active', true)->first();
    }

    public function teachingDisciplines()
    {
        return $this->hasMany(ClassOfferingDiscipline::class, 'teacher_id');
    }

    public function supervisedAssignments()
    {
        return $this->hasMany(SupervisorAssignment::class, 'supervisor_id');
    }

    public function supervisedCourses()
    {
        return $this->belongsToMany(Course::class, 'supervisor_course_unit', 'supervisor_id', 'course_id')
                    ->withPivot('unit_id', 'active')
                    ->withTimestamps();
    }

}

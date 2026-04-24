<?php

namespace App\Services;

use App\Models\ClassOffering;
use App\Models\Course;
use App\Models\Discipline;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class VisibilityService
{
    /**
     * Aplica filtro de visibilidade na query.
     */
    public function apply(
        Builder $query,
        User $user,
        string $context = 'admin' // admin | self
    ): Builder {
        $model = $query->getModel();

        // ADMIN vê tudo
        if ($user->isAdmin()) {
            return $query;
        }

        // Minha área
        if ($context === 'self') {
            return $this->applySelfVisibility($query, $user, $model);
        }

        // Regras por model
        return match (true) {
            $model instanceof ClassOffering => $this->applyClassOfferingVisibility($query, $user),
            $model instanceof Course        => $this->applyCourseVisibility($query, $user),
            $model instanceof Discipline    => $this->applyDisciplineVisibility($query, $user),
            default                         => $this->applyGenericVisibility($query, $user, $model),
        };
    }

    /**
     * Verifica acesso a uma turma específica.
     */
    public function canAccessOffering(User $user, ClassOffering $offering): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCoordenadorAdjunto()) {
            return $offering->unit_id === $user->unit_id;
        }

        if ($user->isCoordenadorGeral() || $user->isCoordenadorAdjuntoGeral()) {
            return optional($offering->unit)->institution_id === $user->resolvedInstitutionId();
        }

        if ($offering->disciplines()
            ->where('teacher_id', $user->id)
            ->exists()) {
            return true;
        }

        if ($user->scholarshipHolder) {
            return $offering->scholarshipHolders()
                ->where('scholarship_holder_id', $user->scholarshipHolder->id)
                ->exists();
        }

        return false;
    }

    /**
     * Contexto "minha área".
     */
    protected function applySelfVisibility(
        Builder $query,
        User $user,
        Model $model
    ): Builder {
        return $query->where(function ($q) use ($user, $model) {

            // Turmas
            if ($model instanceof ClassOffering) {

                // Professor
                $q->orWhereHas('disciplines', function ($sub) use ($user) {
                    $sub->where('teacher_id', $user->id);
                });

                // Bolsista
                if ($user->scholarshipHolder) {
                    $q->orWhereHas('scholarshipHolders', function ($sub) use ($user) {
                        $sub->where('scholarship_holder_id', $user->scholarshipHolder->id);
                    });
                }
            }

            // Models com scholarship_holder_id direto
            if ($user->scholarshipHolder && $this->hasColumn($model, 'scholarship_holder_id')) {
                $q->orWhere('scholarship_holder_id', $user->scholarshipHolder->id);
            }

            // Models com relação singular
            if ($user->scholarshipHolder && method_exists($model, 'scholarshipHolder')) {
                $q->orWhereHas('scholarshipHolder', function ($sub) use ($user) {
                    $sub->where('id', $user->scholarshipHolder->id);
                });
            }
        });
    }

    /**
     * Turmas.
     */
    protected function applyClassOfferingVisibility(
        Builder $query,
        User $user
    ): Builder {
        return $query->where(function ($q) use ($user) {

            // Coordenação unidade
            if ($user->isCoordenadorAdjunto()) {
                $q->orWhere('unit_id', $user->unit_id);
            }

            // Coordenação geral
            if ($user->isCoordenadorGeral() || $user->isCoordenadorAdjuntoGeral()) {
                $q->orWhereHas('unit', function ($sub) use ($user) {
                    $sub->where('institution_id', $user->resolvedInstitutionId());
                });
            }

            // Professor
            $q->orWhereHas('disciplines', function ($sub) use ($user) {
                $sub->where('teacher_id', $user->id);
            });

            // Bolsista vinculado
            if ($user->scholarshipHolder) {
                $q->orWhereHas('scholarshipHolders', function ($sub) use ($user) {
                    $sub->where('scholarship_holder_id', $user->scholarshipHolder->id);
                });
            }
        });
    }

    /**
     * Cursos.
     */
    protected function applyCourseVisibility(
        Builder $query,
        User $user
    ): Builder {
        if ($user->isCoordenadorGeral() || $user->isCoordenadorAdjuntoGeral()) {
            return $query->whereHas('classOfferings.unit', function ($q) use ($user) {
                $q->where('institution_id', $user->resolvedInstitutionId());
            });
        }

        if ($user->isCoordenadorAdjunto()) {
            return $query->whereHas('classOfferings', function ($q) use ($user) {
                $q->where('unit_id', $user->unit_id);
            });
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * Disciplinas.
     */
    protected function applyDisciplineVisibility(
        Builder $query,
        User $user
    ): Builder {
        if ($user->isCoordenadorGeral() || $user->isCoordenadorAdjuntoGeral()) {
            return $query->whereHas('course.classOfferings.unit', function ($q) use ($user) {
                $q->where('institution_id', $user->resolvedInstitutionId());
            });
        }

        if ($user->isCoordenadorAdjunto()) {
            return $query->whereHas('course.classOfferings', function ($q) use ($user) {
                $q->where('unit_id', $user->unit_id);
            });
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * Models genéricos.
     */
    protected function applyGenericVisibility(
        Builder $query,
        User $user,
        Model $model
    ): Builder {
        // Models com unit_id
        if ($this->hasColumn($model, 'unit_id')) {

            if ($user->isCoordenadorGeral() || $user->isCoordenadorAdjuntoGeral()) {
                return $query->whereHas('unit', function ($q) use ($user) {
                    $q->where('institution_id', $user->resolvedInstitutionId());
                });
            }

            if ($user->isCoordenadorAdjunto()) {
                return $query->where('unit_id', $user->unit_id);
            }
        }

        // Models com scholarship_holder_id
        if ($user->scholarshipHolder && $this->hasColumn($model, 'scholarship_holder_id')) {
            return $query->where('scholarship_holder_id', $user->scholarshipHolder->id);
        }

        // Relação singular
        if ($user->scholarshipHolder && method_exists($model, 'scholarshipHolder')) {
            return $query->whereHas('scholarshipHolder', function ($q) use ($user) {
                $q->where('id', $user->scholarshipHolder->id);
            });
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * Verifica se a tabela possui a coluna.
     */
    protected function hasColumn(Model $model, string $column): bool
    {
        return Schema::hasColumn($model->getTable(), $column);
    }
}
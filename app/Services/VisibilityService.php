<?php

namespace App\Services;

use App\Models\ClassOffering;
use App\Models\Course;
use App\Models\Discipline;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class VisibilityService
{
    public function apply(Builder $query, User $user, string $context = 'admin'): Builder
    {
        $model = $query->getModel();

        if ($context === 'self') {
            return $this->applySelfVisibility($query, $user, $model);
        }

        if ($user->hasRole('superadmin')) {
            return $this->applyInstitutionVisibility($query, $user, $model, false);
        }

        if ($user->isInstitutionScoped()) {
            return $this->applyInstitutionVisibility($query, $user, $model);
        }

        if ($user->isUnitScoped()) {
            return $this->applyUnitVisibility($query, $user, $model);
        }

        if ($user->assignments()->where('active', true)->exists()) {
            return $this->applyAssignmentVisibility($query, $user, $model);
        }

        return match (true) {
            $model instanceof ClassOffering => $this->applyClassOfferingVisibility($query, $user),
            $model instanceof Course => $this->applyCourseVisibility($query, $user),
            $model instanceof Discipline => $this->applyDisciplineVisibility($query, $user),
            default => $this->applyGenericVisibility($query, $user, $model),
        };
    }

    public function canAccessOffering(User $user, ClassOffering $offering): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->isInstitutionScoped()) {
            return $user->activeInstitutionIds()->contains($offering->unit?->institution_id);
        }

        if ($user->isUnitScoped()) {
            return $user->visibleUnitIds()->contains($offering->unit_id);
        }

        if ($user->assignments()->where('class_offering_id', $offering->id)->where('active', true)->exists()) {
            return true;
        }

        if ($offering->disciplines()->where('teacher_id', $user->id)->exists()) {
            return true;
        }

        if ($user->scholarshipHolder) {
            return $offering->scholarshipHolders()
                ->where('scholarship_holder_id', $user->scholarshipHolder->id)
                ->exists();
        }

        return false;
    }

    protected function applySelfVisibility(Builder $query, User $user, Model $model): Builder
    {
        return $query->where(function ($q) use ($user, $model) {
            if ($model instanceof ClassOffering) {
                $q->orWhereHas('disciplines', function ($sub) use ($user) {
                    $sub->where('teacher_id', $user->id);
                });

                if ($user->scholarshipHolder) {
                    $q->orWhereHas('scholarshipHolders', function ($sub) use ($user) {
                        $sub->where('scholarship_holder_id', $user->scholarshipHolder->id);
                    });
                }
            }

            if ($user->scholarshipHolder && $this->hasColumn($model, 'scholarship_holder_id')) {
                $q->orWhere('scholarship_holder_id', $user->scholarshipHolder->id);
            }

            if ($user->scholarshipHolder && method_exists($model, 'scholarshipHolder')) {
                $q->orWhereHas('scholarshipHolder', function ($sub) use ($user) {
                    $sub->where('id', $user->scholarshipHolder->id);
                });
            }
        });
    }

    protected function applyInstitutionVisibility(Builder $query, User $user, Model $model, bool $denyWhenEmpty = true): Builder
    {
        $institutionIds = $user->activeInstitutionIds();

        if ($institutionIds->isEmpty()) {
            return $denyWhenEmpty ? $query->whereRaw('1 = 0') : $query;
        }

        $projectIds = $user->visibleProjectIds();
        $unitIds = $user->visibleUnitIds();

        if ($model instanceof Project) {
            return $query->whereIn('institution_id', $institutionIds);
        }

        if ($model instanceof ClassOffering) {
            return $query->whereHas('unit', function ($sub) use ($institutionIds) {
                $sub->whereIn('institution_id', $institutionIds);
            });
        }

        if ($model instanceof Course) {
            return $query->where(function ($sub) use ($institutionIds) {
                $sub->whereIn('institution_id', $institutionIds)
                    ->orWhereHas('classOfferings.unit', function ($nested) use ($institutionIds) {
                        $nested->whereIn('institution_id', $institutionIds);
                    });
            });
        }

        if ($model instanceof Discipline) {
            return $query->where(function ($sub) use ($institutionIds) {
                $sub->whereHas('course', function ($nested) use ($institutionIds) {
                    $nested->whereIn('institution_id', $institutionIds)
                        ->orWhereHas('classOfferings.unit', function ($deep) use ($institutionIds) {
                            $deep->whereIn('institution_id', $institutionIds);
                        });
                });
            });
        }

        return $query->where(function ($scoped) use ($model, $institutionIds, $projectIds, $unitIds) {
            $applied = false;

            if ($this->hasColumn($model, 'institution_id')) {
                $scoped->whereIn('institution_id', $institutionIds);
                $applied = true;
            }

            if ($this->hasColumn($model, 'unit_id') && $unitIds->isNotEmpty()) {
                $scoped->when($applied, fn ($q) => $q)->whereIn('unit_id', $unitIds);
                $applied = true;
            } elseif (method_exists($model, 'unit')) {
                $scoped->whereHas('unit', function ($sub) use ($institutionIds) {
                    $sub->whereIn('institution_id', $institutionIds);
                });
                $applied = true;
            }

            if ($this->hasColumn($model, 'project_id') && $projectIds->isNotEmpty()) {
                $scoped->when($applied, fn ($q) => $q)->whereIn('project_id', $projectIds);
                $applied = true;
            } elseif (method_exists($model, 'project') && $projectIds->isNotEmpty()) {
                $scoped->whereHas('project', function ($sub) use ($projectIds) {
                    $sub->whereIn('projects.id', $projectIds);
                });
                $applied = true;
            }

            if (! $applied) {
                $scoped->whereRaw('1 = 0');
            }
        });
    }

    protected function applyUnitVisibility(Builder $query, User $user, Model $model): Builder
    {
        $unitIds = $user->visibleUnitIds();

        if ($unitIds->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        $projectIds = $user->visibleProjectIds();

        if ($model instanceof Project) {
            return $query->whereHas('units', function ($sub) use ($unitIds) {
                $sub->whereIn('units.id', $unitIds);
            });
        }

        if ($model instanceof ClassOffering) {
            return $query->whereIn('unit_id', $unitIds);
        }

        if ($model instanceof Course) {
            return $query->whereHas('classOfferings', function ($sub) use ($unitIds) {
                $sub->whereIn('unit_id', $unitIds);
            });
        }

        if ($model instanceof Discipline) {
            return $query->whereHas('classOfferings', function ($sub) use ($unitIds) {
                $sub->whereIn('unit_id', $unitIds);
            });
        }

        return $query->where(function ($scoped) use ($model, $unitIds, $projectIds, $user) {
            $applied = false;

            if ($this->hasColumn($model, 'unit_id')) {
                $scoped->whereIn('unit_id', $unitIds);
                $applied = true;
            } elseif (method_exists($model, 'unit')) {
                $scoped->whereHas('unit', function ($sub) use ($unitIds) {
                    $sub->whereIn('units.id', $unitIds);
                });
                $applied = true;
            }

            if ($this->hasColumn($model, 'project_id') && $projectIds->isNotEmpty()) {
                $scoped->whereIn('project_id', $projectIds);
                $applied = true;
            } elseif (method_exists($model, 'project') && $projectIds->isNotEmpty()) {
                $scoped->whereHas('project', function ($sub) use ($projectIds) {
                    $sub->whereIn('projects.id', $projectIds);
                });
                $applied = true;
            }

            if ($user->hasRole('bolsista') && $user->scholarshipHolder && $this->hasColumn($model, 'scholarship_holder_id')) {
                $scoped->where('scholarship_holder_id', $user->scholarshipHolder->id);
                $applied = true;
            }

            if (! $applied) {
                $scoped->whereRaw('1 = 0');
            }
        });
    }

    protected function applyClassOfferingVisibility(Builder $query, User $user): Builder
    {
        return $query->where(function ($q) use ($user) {
            $q->orWhereHas('disciplines', function ($sub) use ($user) {
                $sub->where('teacher_id', $user->id);
            });

            if ($user->scholarshipHolder) {
                $q->orWhereHas('scholarshipHolders', function ($sub) use ($user) {
                    $sub->where('scholarship_holder_id', $user->scholarshipHolder->id);
                });
            }
        });
    }

    protected function applyCourseVisibility(Builder $query, User $user): Builder
    {
        $assignment = $user->assignments()->where('active', true);
        $courseIds = $assignment->whereNotNull('course_id')->pluck('course_id')->unique()->values();
        $offeringIds = $user->assignments()->where('active', true)->whereNotNull('class_offering_id')->pluck('class_offering_id')->unique()->values();
        $projectIds = $user->visibleProjectIds();

        return $query->where(function ($scoped) use ($courseIds, $offeringIds, $projectIds) {
            if ($courseIds->isNotEmpty()) {
                $scoped->orWhereIn('id', $courseIds);
            }

            if ($offeringIds->isNotEmpty()) {
                $scoped->orWhereHas('classOfferings', function ($sub) use ($offeringIds) {
                    $sub->whereIn('class_offerings.id', $offeringIds);
                });
            }

            if ($projectIds->isNotEmpty()) {
                $scoped->orWhereHas('projects', function ($sub) use ($projectIds) {
                    $sub->whereIn('projects.id', $projectIds);
                });
            }
        });
    }

    protected function applyDisciplineVisibility(Builder $query, User $user): Builder
    {
        $courseIds = $user->assignments()
            ->where('active', true)
            ->whereNotNull('course_id')
            ->pluck('course_id')
            ->unique()
            ->values();

        $offeringIds = $user->assignments()
            ->where('active', true)
            ->whereNotNull('class_offering_id')
            ->pluck('class_offering_id')
            ->unique()
            ->values();

        return $query->where(function ($scoped) use ($courseIds, $offeringIds, $user) {
            if ($courseIds->isNotEmpty()) {
                $scoped->orWhereIn('course_id', $courseIds);
            }

            if ($offeringIds->isNotEmpty()) {
                $scoped->orWhereHas('classOfferings', function ($sub) use ($offeringIds) {
                    $sub->whereIn('class_offerings.id', $offeringIds);
                });
            }

            $scoped->orWhereHas('classOfferings', function ($sub) use ($user) {
                $sub->wherePivot('teacher_id', $user->id);
            });
        });
    }

    protected function applyGenericVisibility(Builder $query, User $user, Model $model): Builder
    {
        if ($user->scholarshipHolder && $this->hasColumn($model, 'scholarship_holder_id')) {
            return $query->where('scholarship_holder_id', $user->scholarshipHolder->id);
        }

        if ($user->scholarshipHolder && method_exists($model, 'scholarshipHolder')) {
            return $query->whereHas('scholarshipHolder', function ($scoped) use ($user) {
                $scoped->where('id', $user->scholarshipHolder->id);
            });
        }

        return $query->whereRaw('1 = 0');
    }

    protected function applyAssignmentVisibility(Builder $query, User $user, Model $model): Builder
    {
        $assignments = $user->assignments()->where('active', true)->get();

        $unitIds = $assignments->pluck('unit_id')->filter()->unique()->values();
        $courseIds = $assignments->pluck('course_id')->filter()->unique()->values();
        $projectIds = $assignments->pluck('project_id')->filter()->unique()->values();
        $offeringIds = $assignments->pluck('class_offering_id')->filter()->unique()->values();

        if ($model instanceof Project && $projectIds->isNotEmpty()) {
            return $query->whereIn('id', $projectIds);
        }

        if ($model instanceof Course) {
            return $this->applyCourseVisibility($query, $user);
        }

        if ($model instanceof Discipline) {
            return $this->applyDisciplineVisibility($query, $user);
        }

        if ($model instanceof ClassOffering) {
            return $query->where(function ($scoped) use ($unitIds, $courseIds, $projectIds, $offeringIds) {
                if ($offeringIds->isNotEmpty()) {
                    $scoped->orWhereIn('id', $offeringIds);
                }

                if ($unitIds->isNotEmpty()) {
                    $scoped->orWhereIn('unit_id', $unitIds);
                }

                if ($courseIds->isNotEmpty()) {
                    $scoped->orWhereIn('course_id', $courseIds);
                }

                if ($projectIds->isNotEmpty()) {
                    $scoped->orWhereIn('project_id', $projectIds);
                }
            });
        }

        return $query->where(function ($scoped) use ($model, $unitIds, $courseIds, $projectIds, $offeringIds) {
            $applied = false;

            if ($unitIds->isNotEmpty() && $this->hasColumn($model, 'unit_id')) {
                $scoped->orWhereIn('unit_id', $unitIds);
                $applied = true;
            }

            if ($courseIds->isNotEmpty() && $this->hasColumn($model, 'course_id')) {
                $scoped->orWhereIn('course_id', $courseIds);
                $applied = true;
            }

            if ($projectIds->isNotEmpty() && $this->hasColumn($model, 'project_id')) {
                $scoped->orWhereIn('project_id', $projectIds);
                $applied = true;
            }

            if ($offeringIds->isNotEmpty() && $this->hasColumn($model, 'class_offering_id')) {
                $scoped->orWhereIn('class_offering_id', $offeringIds);
                $applied = true;
            }

            if (! $applied) {
                $scoped->whereRaw('1 = 0');
            }
        });
    }

    protected function hasColumn(Model $model, string $column): bool
    {
        return Schema::hasColumn($model->getTable(), $column);
    }
}

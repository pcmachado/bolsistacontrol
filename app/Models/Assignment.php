<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    const TYPE_PROFESSOR = 'professor';

    const TYPE_SUPERVISOR = 'supervisor';

    const TYPE_COORDENADOR_ADJUNTO = 'coordenador_adjunto';

    const TYPE_COORDENADOR_GERAL = 'coordenador_geral';

    const TYPE_COORDENADOR_ADJUNTO_GERAL = 'coordenador_adjunto_geral';

    const TYPE_APOIO = 'apoio';

    protected $fillable = [
        'user_id',
        'project_id',
        'course_id',
        'class_offering_id',
        'unit_id',
        'assignment_type',
        'position_id',
        'start_date',
        'end_date',
        'active',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function classOffering(): BelongsTo
    {
        return $this->belongsTo(ClassOffering::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}

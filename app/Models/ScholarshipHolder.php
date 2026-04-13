<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ScholarshipHolder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'cpf',
        'email',
        'phone',
        'pix_key',
        'bank',
        'agency',
        'account',
        'user_id',
        'unit_id',
        'start_date',
        'end_date',
        'status',
    ];
    
    // Criptografa os dados bancários antes de salvar no banco
    public function setBankAttribute($value): void {
        $this->attributes['bank'] = Crypt::encryptString($value);
    }

    public function getBankAttribute($value): ?string {
        return Crypt::decryptString($value);
    }

    public function setAgencyAttribute($value): void {
        $this->attributes['agency'] = Crypt::encryptString($value);
    }

    public function getAgencyAttribute($value): ?string {
        return Crypt::decryptString($value);
    }

    public function setAccountAttribute($value): void {
        $this->attributes['account'] = Crypt::encryptString($value);
    }

    public function getPixKeyAttribute($value): ?string {
        return Crypt::decryptString($value);
    }

    public function setPixKeyAttribute($value): void {
        $this->attributes['pix_key'] = Crypt::encryptString($value);
    }   
    
    public function getAccountAttribute($value): ?string {
        return Crypt::decryptString($value);
    }

    // Relacionamento: um bolsista pertence a um usuário (para autenticação)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    // Relacionamento: um bolsista pode ter muitos registros de frequência
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function attendanceSubmissions()
    {
        return $this->hasMany(AttendanceSubmission::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_scholarship_holder')
                    ->withTimestamps();
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_scholarship_holder')
                    ->withPivot('position_id', 'weekly_workload','status')
                    ->withTimestamps();
    }

    public function classOfferings()
    {
        return $this->belongsToMany(ClassOffering::class, 'scholarship_holder_class_offering')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function fundingSources(): BelongsToMany
    {
        return $this->belongsToMany(FundingSource::class, 'scholarship_holder_funding_source')
                    ->withTimestamps();
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

}
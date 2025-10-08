<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScholarshipHolder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'cpf', 'email', 'phone', 'bank', 'agency', 'account',
         'user_id', 'unit_id', 'start_date', 'end_date'  
    ];
    
    // Criptografa os dados bancários antes de salvar no banco
    public function setBankAttribute($value): void { $this->attributes['bank'] = Crypt::encryptString($value); }
    public function getBankAttribute($value): string { return Crypt::decryptString($value); }

    public function setAgencyAttribute($value): void { $this->attributes['agency'] = Crypt::encryptString($value); }
    public function getAgencyAttribute($value): string { return Crypt::decryptString($value); }

    public function setAccountAttribute($value): void { $this->attributes['account'] = Crypt::encryptString($value); }
    public function getAccountAttribute($value): string { return Crypt::decryptString($value); }

    // Relacionamento: um bolsista pertence a um usuário (para autenticação)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    // Relacionamento: um bolsista pode ter muitas notificações
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_scholarship_holder');
    }

}

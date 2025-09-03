<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class ScholarshipHolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'cpf', 'email', 'phone', 'role_id', 'user_id',
        'bank', 'agency', 'account'
    ];
    
    // Criptografa os dados bancários antes de salvar no banco
    public function setBankAttribute($value): void { $this->attributes['bank'] = Crypt::encryptString($value); }
    public function getBankAttribute($value): string { return Crypt::decryptString($value); }

    public function setAgencyAttribute($value): void { $this->attributes['agency'] = Crypt::encryptString($value); }
    public function getAgencyAttribute($value): string { return Crypt::decryptString($value); }

    public function setAccountAttribute($value): void { $this->attributes['account'] = Crypt::encryptString($value); }
    public function getAccountAttribute($value): string { return Crypt::decryptString($value); }

    // Relacionamento: um bolsista pertence a um cargo
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // Relacionamento: um bolsista pertence a um usuário (para autenticação)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    // Relacionamento: um bolsista pode estar em várias unidades
    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'scholarship_holder_units')
                    ->withPivot('monthly_workload', 'start_date', 'end_date')
                    ->withTimestamps();
    }
}

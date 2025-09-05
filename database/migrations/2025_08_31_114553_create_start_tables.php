<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabela de usuários. Adicionamos o campo 'role'
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('user'); // papel do usuário
            $table->rememberToken();
            $table->timestamps();
        });

        // Tabela para bolsistas
        Schema::create('scholarship_holders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cpf')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('bank')->nullable();
            $table->string('agency')->nullable();
            $table->string('account')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->timestamps();
        });

        // Tabela de unidades
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city');
            $table->string('address')->nullable();
            $table->timestamps();
        });

        // Tabela para registro de frequência
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scholarship_holder_id');
            $table->unsignedBigInteger('unit_id');
            $table->date('date');
            $table->time('entry_time');
            $table->time('exit_time');
            $table->text('observation')->nullable();
            $table->boolean('approved')->default(false); // para homologação
            $table->timestamps();
        });
        
        // Tabela para notificações
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scholarshipholder_id');
            $table->string('type'); // 'atraso', 'pendencia', 'falta'
            $table->text('message');
            $table->boolean('read')->default(false);
            $table->timestamps();
        });

        // Tabela para cargos
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Tabela para o vínculo entre bolsistas e unidades (M:N)
        Schema::create('scholarship_holder_unit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scholarship_holder_id');
            $table->unsignedBigInteger('unit_id');
            $table->float('monthly_workload');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarship_holder_unit');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('units');
        Schema::dropIfExists('scholarship_holders');
        Schema::dropIfExists('users');
    }
};

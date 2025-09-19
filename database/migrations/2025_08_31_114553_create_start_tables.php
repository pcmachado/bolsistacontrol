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
            $table->softDeletes();
        });

        // Tabela para cadastro dos projetos - contratos
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex.: PIBIC, Extensão, Monitoria
            $table->text('description')->nullable();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->date('start_date')->nullable(); // início da vigência do contrato
            $table->date('end_date')->nullable();   // fim da vigência
            $table->timestamps();
            $table->softDeletes();
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
            $table->string('institution_link')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('scholarship_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->date('start_date')->nullable(); // quando o bolsista iniciou
            $table->date('end_date')->nullable();   // fim do vínculo
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabela de unidades
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('city');
            $table->string('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabela para registro de frequência
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scholarship_holder_id');
            $table->date('date');
            $table->time('entry_time')->nullable();
            $table->time('exit_time')->nullable();
            $table->text('observation')->nullable();
            $table->integer('hours');
            $table->decimal('calculated_value', 10, 2)->nullable(); // valor calculado
            $table->boolean('approved')->default(false); // para homologação
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Tabela para notificações
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scholarshipholder_id');
            $table->string('type'); // 'atraso', 'pendencia', 'falta'
            $table->text('message');
            $table->boolean('read')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabela para cargos
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabela para o vínculo entre bolsistas e projetos (M:N)
        Schema::create('project_scholarship_holders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('scholarship_holder_id')->constrained()->onDelete('cascade');
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->float('monthly_workload');
            $table->date('start_date');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city');
            $table->string('state');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_scholarship_holders');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('units');
        Schema::dropIfExists('scholarship_holders');
        Schema::dropIfExists('users');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('user_unit');
        Schema::dropIfExists('institutions');
    }
};

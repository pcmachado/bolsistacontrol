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
        // 1. Instituições
        Schema::create('instituitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city');
            $table->string('state');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        // 2. Usuários (já existe na migration padrão do Laravel)

        // 3. Cargos
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Projetos
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex.: PIBIC, Extensão, Monitoria
            $table->text('description')->nullable();
            $table->foreignId('instituition_id')->constrained()->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Unidades
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instituition_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('city');
            $table->string('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Bolsistas
        Schema::create('scholarship_holders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cpf')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('bank')->nullable();
            $table->string('agency')->nullable();
            $table->string('account')->nullable();
            $table->string('instituition_link')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 7. Registro de frequência
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_holder_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('entry_time')->nullable();
            $table->time('exit_time')->nullable();
            $table->text('observation')->nullable();
            $table->integer('hours');
            $table->decimal('calculated_value', 10, 2)->nullable();
            $table->boolean('approved')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // 8. Notificações
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_holder_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'atraso', 'pendencia', 'falta'
            $table->text('message');
            $table->boolean('read')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // 9. Relação M:N entre projetos e bolsistas
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

        // 10. Relação M:N entre usuários e unidades
        Schema::create('user_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('bolsista'); // 'admin', 'user'
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_unit');
        Schema::dropIfExists('project_scholarship_holders');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('scholarship_holders');
        Schema::dropIfExists('units');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('instituitions');
    }
};

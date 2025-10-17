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
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city');
            $table->string('state');
            $table->string('address')->nullable();
            $table->string('cnpj')->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('acronym')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('complement')->nullable();
            $table->string('number')->nullable();
            $table->string('country')->default('Brasil');
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
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Unidades
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('city');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('cnpj')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            // adiciona a coluna unit_id como chave estrangeira
            $table->foreignId('unit_id')
                  ->nullable()
                  ->constrained('units')
                  ->onDelete('set null');
        });

        // 6. Bolsistas
        Schema::create('scholarship_holders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cpf')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('bank')->nullable();
            $table->text('agency')->nullable();
            $table->text('account')->nullable();
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
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('observation')->nullable();
            $table->integer('hours');
            $table->decimal('calculated_value', 10, 2)->nullable();
            $table->boolean('approved')->default(false);
            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedBigInteger('approved_by_user_id')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreign('approved_by_user_id')->references('id')->on('users')->onDelete('set null');
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

        // Tabela para Cursos
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('duration_hours')->nullable();
            $table->text('prerequisites')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabela para Fontes Pagadoras
        Schema::create('funding_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['internal', 'external'])->default('external');
            $table->text('description')->nullable();
            $table->text('contact_info')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 9. Relação M:N entre projetos e bolsistas
        Schema::create('project_scholarship_holders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('scholarship_holder_id')->constrained()->onDelete('cascade');
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->integer('weekly_workload')->default(20);
            $table->text('assignments')->nullable(); // Descrição das atribuições
            $table->decimal('hourly_rate', 8, 2)->nullable(); // Valor da hora de trabalho
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->date('end_date')->nullable();
            $table->date('start_date');
            $table->timestamps();
            $table->softDeletes();
        });

         // Um projeto pode ter vários cursos
        Schema::create('project_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('semester')->nullable();
            $table->integer('year')->nullable();
            $table->boolean('active')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('capacity')->nullable();
            $table->enum('status', ['planned', 'ongoing', 'completed'])->default('planned');
            $table->timestamps();
            $table->softDeletes();
        });

        // Um projeto pode ter várias fontes pagadoras
        Schema::create('project_funding_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('funding_source_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'finished'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Um bolsista pode estar em vários cursos
        Schema::create('course_scholarship_holders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('scholarship_holder_id')->constrained()->onDelete('cascade');
            $table->date('enrollment_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->enum('status', ['enrolled', 'completed', 'dropped'])->default('enrolled');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabela Pivot para a relação N:M entre Projetos e Cargos
        // Esta tabela conterá os atributos específicos da relação
        Schema::create('project_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Tabela para Atribuições (Assignments)
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_holder_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_position_id')->constrained('project_positions')->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('project_positions');
        Schema::dropIfExists('project_scholarship_holders');
        Schema::dropIfExists('project_courses');
        Schema::dropIfExists('project_funding_sources');
        Schema::dropIfExists('course_scholarship_holders');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('scholarship_holders');
        Schema::dropIfExists('units');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('institutions');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('funding_sources');
    }
};

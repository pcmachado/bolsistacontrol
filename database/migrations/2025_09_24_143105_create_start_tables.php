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

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('shortname')->nullable();
            $table->string('city');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('domain')->nullable();
            $table->string('cnpj')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'institution_id')) {
                $table->foreignId('institution_id')->nullable()->constrained('institutions')->onDelete('set null');
            }
            if (!Schema::hasColumn('users', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
            }
        });

        Schema::create('scholarship_holders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cpf')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('bank')->nullable();
            $table->text('agency')->nullable();
            $table->text('account')->nullable();
            $table->text('pix_key')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('attendance_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_holder_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->enum('status', ['draft','submitted','approved','rejected'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(
                ['scholarship_holder_id', 'year', 'month'],
                'unique_submission_per_month'
            );
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_holder_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('description')->nullable();
            $table->integer('hours');
            $table->decimal('calculated_value', 10, 2)->nullable();
            $table->boolean('approved')->default(false);
            $table->enum('status', ['draft','submitted','approved','rejected','late'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedBigInteger('approved_by_user_id')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->foreign('approved_by_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreignId('attendance_submission_id')->nullable()->constrained('attendance_submissions')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

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

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex.: PIBIC, Extensão, Monitoria
            $table->text('description')->nullable();
            $table->string('wizard_step')->default('step1');
            $table->string('status')->default('draft');
            $table->foreignId('institution_id')->constrained()->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('course_scholarship_holder', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('scholarship_holder_id')->constrained()->onDelete('cascade');
            $table->date('enrollment_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->enum('status', ['enrolled', 'completed', 'dropped'])->default('enrolled');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('disciplines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('workload')->nullable(); // carga horária em horas
            $table->unsignedInteger('sequence_order')->nullable(); // ordem no curso
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('class_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable(); // ex: Turma A noite
            $table->string('semester')->nullable(); // ex: 2025/1
            $table->year('year')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->boolean('active')->default(true);
            $table->enum('status', ['planned', 'ongoing', 'completed', 'cancelled', 'finished'])->default('planned'); // planned, ongoing, finished, cancelled
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('project_scholarship_holder', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('scholarship_holder_id')->constrained()->onDelete('cascade');
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->integer('weekly_workload')->default(20);
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->date('end_date')->nullable();
            $table->date('start_date');
            $table->text('assignments')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('funding_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->enum('type', ['internal', 'external'])->default('external');
            $table->text('description')->nullable();
            $table->text('contact_info')->nullable();
            $table->text('address')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('used_amount', 12, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('project_funding_source', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('funding_source_id')->constrained()->onDelete('cascade');
            $table->decimal('allocated_amount', 12, 2)->nullable();
            $table->decimal('used_amount', 12, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'finished'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('project_position', function (Blueprint $table) {
            $table->id();
            $table->text('assignments')->nullable(); // Descrição das atribuições
            $table->decimal('hourly_rate', 8, 2)->nullable(); // Valor da hora de trabalho
            $table->integer('weekly_workload')->default(20); // Limite semanal de horas
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('class_offering_discipline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_offering_id')->constrained()->cascadeOnDelete();
            $table->foreignId('discipline_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('workload')->nullable(); // pode sobrescrever carga padrão
            $table->string('schedule')->nullable(); // ex: "2ª e 4ª - 19h às 22h"
            $table->string('room')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('supervisor_assignment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['supervisor_id', 'course_id', 'unit_id']);
            $table->softDeletes();
        });

        Schema::create('scholarship_holder_class_offering', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_holder_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_offering_id')->constrained()->cascadeOnDelete();
            $table->string('role')->nullable(); // ex: aluno, monitor, bolsista
            $table->timestamps();

            $table->unique(
                ['scholarship_holder_id', 'class_offering_id']
            ,
            'unique_scholarship_class'
            );
            $table->softDeletes();
        });

        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('class_offering_id')->constrained()->cascadeOnDelete();
            $table->foreignId('discipline_id')->constrained()->cascadeOnDelete();

            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');

            $table->decimal('duration_hours', 4, 2); // ex: 2.0, 3.5, 4.0

            $table->string('status')->default('finished'); // finished, planned, cancelled

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('intelligent_alert_settings', function (Blueprint $table) {
            $table->id();

            // Dias sem aula
            $table->integer('no_class_days')->default(10);

            // Percentual mínimo ministrado vs tempo decorrido
            $table->decimal('delay_percent_threshold', 5, 2)->default(0.80);

            // Ativar/desativar regras
            $table->boolean('check_delays_enabled')->default(true);
            $table->boolean('check_no_class_enabled')->default(true);

            // Quem recebe notificações (id do papel)
            $table->string('delay_notify_roles')->default('coordenador_adjunto,supervisor');
            $table->string('no_class_notify_roles')->default('coordenador_adjunto');

            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Quem recebe
            $table->foreignId('scholarship_holder_id')->constrained()->cascadeOnDelete();

            // Projeto e unidade (ajudam na auditoria)
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('funding_source_id')->nullable()->constrained();

            // Referência do período
            $table->unsignedTinyInteger('month'); // 1–12
            $table->unsignedSmallInteger('year'); // ex: 2025

            // Totais
            $table->decimal('total_hours', 8, 2)->default(0);
            $table->decimal('amount', 10, 2)->default(0);

            // Status do pagamento
            // draft | sent_to_payment | paid | confirmed
            $table->string('status', 30)->default('draft');

            $table->string('receipt_number')->nullable()->unique();
            $table->timestamp('receipt_generated_at')->nullable();
            $table->string('receipt_hash', 64)->nullable()->unique();

            // Datas do fluxo
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();

            // Quem executou o pagamento (usuário financeiro / coord adjunto)
            $table->foreignId('paid_by_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->nullableMorphs('payable'); // Polimórfico para diferentes tipos de pagamento

            // Observações gerais
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Um pagamento por bolsista por mês/ano (se quiser garantir isso)
            $table->unique(
                ['scholarship_holder_id', 'month', 'year', 'project_id'],
                'payment_holder_month_year_project_unique'
            );
        });

        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();

            $table->string('key')->unique(); // ex: payment_receipt
            $table->string('name');          // ex: Recibo de Pagamento
            $table->text('description')->nullable();

            // Conteúdo
            $table->longText('header_html')->nullable();
            $table->longText('body_html');
            $table->longText('footer_html')->nullable();

            // Escopo institucional
            $table->foreignId('institution_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();

            $table->boolean('active')->default(true);

            $table->timestamps();
        });

        Schema::create('financial_closures', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');

            $table->timestamp('closed_at');
            $table->foreignId('closed_by_user_id')->constrained('users');

            $table->timestamps();

            $table->unique(['unit_id', 'month', 'year']);
        });

        Schema::create('financial_logs', function (Blueprint $table) {
            $table->id();

            $table->string('action'); // created, paid, confirmed, closed, reversed
            $table->string('entity_type'); // Payment, Closure
            $table->unsignedBigInteger('entity_id');

            $table->json('metadata')->nullable();

            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });

        Schema::create('final_activity_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('scholarship_holder_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->longText('activities');      // atividades desenvolvidas
            $table->longText('results');         // resultados alcançados
            $table->longText('contributions');   // contribuições ao projeto

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_activity_reports');
        Schema::dropIfExists('financial_logs');
        Schema::dropIfExists('financial_closures');
        Schema::dropIfExists('document_templates');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('supervisor_assignment');
        Schema::dropIfExists('class_sessions');
        Schema::dropIfExists('project_position');
        Schema::dropIfExists('supervisor_course_unit');
        Schema::dropIfExists('scholarship_holder_class_offering');
        Schema::dropIfExists('class_offering_discipline');
        Schema::dropIfExists('project_scholarship_holder');
        Schema::dropIfExists('class_offering');
        Schema::dropIfExists('project_funding_source');
        Schema::dropIfExists('course_scholarship_holder');
        Schema::dropIfExists('disciplines');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('attendance_submissions');
        Schema::dropIfExists('scholarship_holders');
        Schema::dropIfExists('units');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('institutions');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('funding_sources');
    }
};

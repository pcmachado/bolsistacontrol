<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_offerings', function (Blueprint $table) {
            if (! Schema::hasColumn('class_offerings', 'hours_per_day')) {
                $table->decimal('hours_per_day', 5, 2)->nullable()->after('capacity');
            }
        });

        Schema::table('class_offering_disciplines', function (Blueprint $table) {
            if (! Schema::hasColumn('class_offering_disciplines', 'teacher_scholarship_holder_id')) {
                $table->foreignId('teacher_scholarship_holder_id')
                    ->nullable()
                    ->after('teacher_id')
                    ->constrained('scholarship_holders')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('class_offering_disciplines', 'planned_total_hours')) {
                $table->unsignedInteger('planned_total_hours')->nullable()->after('workload');
            }
        });

        Schema::table('student_discipline_month_records', function (Blueprint $table) {
            if (! Schema::hasColumn('student_discipline_month_records', 'class_offering_discipline_id')) {
                $table->unsignedBigInteger(
                    'class_offering_discipline_id'
                )->nullable();

                $table->foreign(
                    'class_offering_discipline_id',
                    'fk_sdmr_cod'
                )
                ->references('id')
                ->on('class_offering_disciplines')
                ->nullOnDelete();
            }

            if (! Schema::hasColumn('student_discipline_month_records', 'classes_in_month')) {
                $table->integer('classes_in_month')->nullable()->after('year');
            }

            if (! Schema::hasColumn('student_discipline_month_records', 'presences')) {
                $table->integer('presences')->nullable()->after('justified_absences');
            }

            $table->unique(
                ['class_offering_discipline_id', 'student_id', 'month', 'year'],
                'student_disc_month_unique_v2'
            );
        });

        Schema::table('student_month_records', function (Blueprint $table) {
            if (! Schema::hasColumn('student_month_records', 'total_classes')) {
                $table->integer('total_classes')->default(0)->after('year');
            }

            if (! Schema::hasColumn('student_month_records', 'total_absences')) {
                $table->integer('total_absences')->default(0)->after('total_classes');
            }

            if (! Schema::hasColumn('student_month_records', 'total_justified_absences')) {
                $table->integer('total_justified_absences')->default(0)->after('total_absences');
            }

            if (! Schema::hasColumn('student_month_records', 'total_presences')) {
                $table->integer('total_presences')->default(0)->after('total_justified_absences');
            }

            if (! Schema::hasColumn('student_month_records', 'estimated_payment_amount')) {
                $table->decimal('estimated_payment_amount', 10, 2)->default(0)->after('total_presences');
            }

            if (! Schema::hasColumn('student_month_records', 'status')) {
                $table->enum('status', ['draft', 'closed', 'sent_to_finance'])
                    ->default('draft')
                    ->after('estimated_payment_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_month_records', function (Blueprint $table) {
            if (Schema::hasColumn('student_month_records', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('student_month_records', 'estimated_payment_amount')) {
                $table->dropColumn('estimated_payment_amount');
            }

            if (Schema::hasColumn('student_month_records', 'total_presences')) {
                $table->dropColumn('total_presences');
            }

            if (Schema::hasColumn('student_month_records', 'total_justified_absences')) {
                $table->dropColumn('total_justified_absences');
            }

            if (Schema::hasColumn('student_month_records', 'total_absences')) {
                $table->dropColumn('total_absences');
            }

            if (Schema::hasColumn('student_month_records', 'total_classes')) {
                $table->dropColumn('total_classes');
            }
        });

        Schema::table('student_discipline_month_records', function (Blueprint $table) {
            $table->dropUnique('student_disc_month_unique_v2');

            if (Schema::hasColumn('student_discipline_month_records', 'presences')) {
                $table->dropColumn('presences');
            }

            if (Schema::hasColumn('student_discipline_month_records', 'classes_in_month')) {
                $table->dropColumn('classes_in_month');
            }

            if (Schema::hasColumn('student_discipline_month_records', 'class_offering_discipline_id')) {
                $table->dropConstrainedForeignId('class_offering_discipline_id');
            }
        });

        Schema::table('class_offering_disciplines', function (Blueprint $table) {
            if (Schema::hasColumn('class_offering_disciplines', 'planned_total_hours')) {
                $table->dropColumn('planned_total_hours');
            }

            if (Schema::hasColumn('class_offering_disciplines', 'teacher_scholarship_holder_id')) {
                $table->dropConstrainedForeignId('teacher_scholarship_holder_id');
            }
        });

        Schema::table('class_offerings', function (Blueprint $table) {
            if (Schema::hasColumn('class_offerings', 'hours_per_day')) {
                $table->dropColumn('hours_per_day');
            }
        });
    }
};

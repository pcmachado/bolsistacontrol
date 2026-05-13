<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->after('institution_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('projects', 'report_header_html')) {
                $table->longText('report_header_html')->nullable()->after('end_date');
            }

            if (! Schema::hasColumn('projects', 'report_footer_html')) {
                $table->longText('report_footer_html')->nullable()->after('report_header_html');
            }

            if (! Schema::hasColumn('projects', 'monthly_report_template_id')) {
                $table->foreignId('monthly_report_template_id')
                    ->nullable()
                    ->after('report_footer_html')
                    ->constrained('document_templates')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('projects', 'final_report_template_id')) {
                $table->foreignId('final_report_template_id')
                    ->nullable()
                    ->after('monthly_report_template_id')
                    ->constrained('document_templates')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'final_report_template_id')) {
                $table->dropForeign(['final_report_template_id']);
                $table->dropColumn('final_report_template_id');
            }

            if (Schema::hasColumn('projects', 'monthly_report_template_id')) {
                $table->dropForeign(['monthly_report_template_id']);
                $table->dropColumn('monthly_report_template_id');
            }

            if (Schema::hasColumn('projects', 'report_footer_html')) {
                $table->dropColumn('report_footer_html');
            }

            if (Schema::hasColumn('projects', 'report_header_html')) {
                $table->dropColumn('report_header_html');
            }

            if (Schema::hasColumn('projects', 'unit_id')) {
                $table->dropForeign(['unit_id']);
                $table->dropColumn('unit_id');
            }
        });
    }
};

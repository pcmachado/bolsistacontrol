<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'document_template_id')) {
                $table->foreignId('document_template_id')
                    ->nullable()
                    ->after('report_footer_html')
                    ->constrained('document_templates')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('projects', 'report_title')) {
                $table->string('report_title')->nullable()->after('name');
            }

            if (! Schema::hasColumn('projects', 'report_subtitle')) {
                $table->string('report_subtitle')->nullable()->after('report_title');
            }

            if (! Schema::hasColumn('projects', 'report_logo_path')) {
                $table->string('report_logo_path')->nullable()->after('report_subtitle');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'document_template_id')) {
                $table->dropForeign(['document_template_id']);
                $table->dropColumn('document_template_id');
            }

            if (Schema::hasColumn('projects', 'report_logo_path')) {
                $table->dropColumn('report_logo_path');
            }

            if (Schema::hasColumn('projects', 'report_subtitle')) {
                $table->dropColumn('report_subtitle');
            }

            if (Schema::hasColumn('projects', 'report_title')) {
                $table->dropColumn('report_title');
            }
        });
    }
};

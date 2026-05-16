<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            if (! Schema::hasColumn('document_templates', 'title')) {
                $table->string('title')->nullable()->after('description');
            }

            if (! Schema::hasColumn('document_templates', 'subtitle')) {
                $table->string('subtitle')->nullable()->after('title');
            }

            if (! Schema::hasColumn('document_templates', 'header_left_logo_path')) {
                $table->string('header_left_logo_path')->nullable()->after('footer_html');
            }

            if (! Schema::hasColumn('document_templates', 'header_center_logo_path')) {
                $table->string('header_center_logo_path')->nullable()->after('header_left_logo_path');
            }

            if (! Schema::hasColumn('document_templates', 'header_right_logo_path')) {
                $table->string('header_right_logo_path')->nullable()->after('header_center_logo_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            foreach ([
                'header_right_logo_path',
                'header_center_logo_path',
                'header_left_logo_path',
                'subtitle',
                'title',
            ] as $column) {
                if (Schema::hasColumn('document_templates', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

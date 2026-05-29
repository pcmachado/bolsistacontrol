<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_offering_disciplines', function (Blueprint $table) {
            if (! Schema::hasColumn('class_offering_disciplines', 'hours_per_day')) {
                $table->decimal('hours_per_day', 5, 2)
                    ->nullable()
                    ->after('planned_total_hours');
            }
        });
    }

    public function down(): void
    {
        Schema::table('class_offering_disciplines', function (Blueprint $table) {
            if (Schema::hasColumn('class_offering_disciplines', 'hours_per_day')) {
                $table->dropColumn('hours_per_day');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_course')) {
            return;
        }

        Schema::create('project_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->boolean('active')->default(true);
            $table->string('semester')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'course_id'], 'project_course_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_course');
    }
};


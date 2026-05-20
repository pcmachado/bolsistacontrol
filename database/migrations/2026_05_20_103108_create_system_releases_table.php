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
        Schema::create('system_releases', function (Blueprint $table) {
            $table->id();
            $table->string('version')->unique(); // Unique version number ex: v1.0.1
            $table->text('release_notes'); // HTML content for release notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_releases');
    }
};

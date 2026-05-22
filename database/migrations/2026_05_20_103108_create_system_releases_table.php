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

            $table->string('version')->unique();

            $table->string('git_tag')->nullable();

            $table->string('git_hash')->nullable();

            $table->text('release_notes');

            $table->json('changes')->nullable();

            $table->boolean('is_visible')
                ->default(true);

            $table->boolean('is_automatic')
                ->default(false);

            $table->timestamp('released_at')
                ->nullable();

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

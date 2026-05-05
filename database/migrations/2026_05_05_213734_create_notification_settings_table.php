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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // tipo do evento (payment_status_changed, submission_approved, etc.)
            $table->string('notification_type'); // tipo de notificação (database, mail, both)
            $table->json('recipients')->nullable(); // quem deve receber (roles, users específicos)
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('institution_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->unique(['event_type', 'project_id', 'institution_id'], 'unique_event_project_institution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};

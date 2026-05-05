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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // identificador único do template
            $table->string('name'); // nome amigável
            $table->text('description')->nullable(); // descrição do template
            $table->text('subject'); // assunto do email
            $table->longText('body_html'); // corpo do email em HTML
            $table->longText('body_text')->nullable(); // versão texto plano
            $table->json('variables')->nullable(); // variáveis disponíveis no template
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('institution_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['key', 'project_id', 'institution_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};

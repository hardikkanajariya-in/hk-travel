<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_template_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('email_template_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 8);
            $table->string('subject');
            $table->longText('body_html');
            $table->longText('body_text')->nullable();
            $table->timestamps();

            $table->unique(['email_template_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_template_translations');
    }
};

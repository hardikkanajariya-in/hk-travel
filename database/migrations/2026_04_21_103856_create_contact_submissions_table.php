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
        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('contact_forms')->cascadeOnDelete();
            $table->json('data');
            $table->string('email')->nullable()->index();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('locale', 8)->nullable();
            $table->string('status', 20)->default('new')->index(); // new, read, handled, spam, archived
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_submissions');
    }
};

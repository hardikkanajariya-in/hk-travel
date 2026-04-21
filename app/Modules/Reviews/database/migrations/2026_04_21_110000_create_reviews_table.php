<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->ulidMorphs('reviewable');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_name', 120)->nullable();
            $table->string('author_email', 180)->nullable();
            $table->string('title', 200)->nullable();
            $table->text('body');
            $table->decimal('rating', 3, 2)->default(0);
            $table->json('criteria')->nullable();
            $table->string('status', 16)->default('pending')->index();
            $table->boolean('is_verified')->default(false);
            $table->unsignedInteger('helpful_count')->default(0);
            $table->unsignedInteger('reported_count')->default(0);
            $table->string('locale', 8)->nullable();
            $table->ipAddress('ip')->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['reviewable_type', 'reviewable_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->ulidMorphs('commentable');
            $table->foreignUlid('parent_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_name', 120)->nullable();
            $table->string('author_email', 180)->nullable();
            $table->string('author_url', 255)->nullable();
            $table->text('body');
            $table->string('status', 16)->default('pending')->index();
            $table->unsignedTinyInteger('depth')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->string('locale', 8)->nullable();
            $table->ipAddress('ip')->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['commentable_type', 'commentable_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};

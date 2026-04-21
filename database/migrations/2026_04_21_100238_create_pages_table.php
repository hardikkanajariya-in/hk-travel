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
        Schema::create('pages', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('layout')->default('default');
            $table->string('status', 16)->default('draft'); // draft | published
            $table->boolean('is_homepage')->default(false);
            $table->boolean('allow_comments')->default(false);
            $table->json('seo')->nullable();
            $table->json('translations')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('is_homepage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('destination_id')->nullable()->index()->constrained('destinations')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->nullable()->index();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->json('highlights')->nullable();
            $table->json('included')->nullable();
            $table->json('schedule')->nullable();
            $table->decimal('duration_hours', 5, 2)->default(2);
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->unsignedSmallInteger('min_age')->default(0);
            $table->unsignedSmallInteger('max_group_size')->default(20);
            $table->string('difficulty')->default('easy');
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->json('seo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};

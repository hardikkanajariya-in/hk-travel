<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('parent_id')->nullable()->index();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('seo')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_tags', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('blog_posts', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('status', 16)->default('draft')->index(); // draft|scheduled|published|archived
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('allow_comments')->default(true);
            $table->boolean('show_toc')->default(true);
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedSmallInteger('reading_minutes')->default(1);
            $table->string('locale', 8)->default('en');
            $table->json('seo')->nullable();
            $table->json('translations')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
        });

        Schema::create('blog_post_category', function (Blueprint $table): void {
            $table->foreignUlid('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignUlid('blog_category_id')->constrained('blog_categories')->cascadeOnDelete();
            $table->primary(['blog_post_id', 'blog_category_id'], 'bpc_pk');
        });

        Schema::create('blog_post_tag', function (Blueprint $table): void {
            $table->foreignUlid('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignUlid('blog_tag_id')->constrained('blog_tags')->cascadeOnDelete();
            $table->primary(['blog_post_id', 'blog_tag_id'], 'bpt_pk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_tag');
        Schema::dropIfExists('blog_post_category');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_categories');
    }
};

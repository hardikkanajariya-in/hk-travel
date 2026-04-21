<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extends the base tours table with the columns needed by the public
 * detail page and search filters: destination FK, media, schedule,
 * inclusions, SEO blob and rating roll-up.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table): void {
            $table->foreignUlid('destination_id')->nullable()->after('id')->index()->constrained('destinations')->nullOnDelete();
            $table->string('cover_image')->nullable()->after('description');
            $table->json('gallery')->nullable()->after('cover_image');
            $table->json('inclusions')->nullable()->after('gallery');
            $table->json('exclusions')->nullable()->after('inclusions');
            $table->json('itinerary')->nullable()->after('exclusions');
            $table->string('difficulty')->default('easy')->after('itinerary');
            $table->string('language')->default('en')->after('difficulty');
            $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            $table->string('currency', 3)->default('USD')->after('discount_price');
            $table->boolean('is_featured')->default(false)->after('is_published')->index();
            $table->decimal('rating_avg', 3, 2)->default(0)->after('is_featured');
            $table->unsignedInteger('rating_count')->default(0)->after('rating_avg');
            $table->json('seo')->nullable()->after('rating_count');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('destination_id');
            $table->dropColumn([
                'cover_image', 'gallery', 'inclusions', 'exclusions', 'itinerary',
                'difficulty', 'language', 'discount_price', 'currency', 'is_featured',
                'rating_avg', 'rating_count', 'seo',
            ]);
        });
    }
};

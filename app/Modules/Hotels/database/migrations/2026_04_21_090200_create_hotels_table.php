<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('destination_id')->nullable()->index()->constrained('destinations')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedTinyInteger('star_rating')->default(3);
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->json('amenities')->nullable();
            $table->string('address')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('check_in')->default('15:00');
            $table->string('check_out')->default('11:00');
            $table->decimal('price_from', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->json('seo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hotel_rooms', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('hotel_id')->index()->constrained('hotels')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price_per_night', 10, 2)->default(0);
            $table->unsignedSmallInteger('capacity_adults')->default(2);
            $table->unsignedSmallInteger('capacity_children')->default(0);
            $table->unsignedSmallInteger('inventory')->default(1);
            $table->boolean('is_available')->default(true);
            $table->json('amenities')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_rooms');
        Schema::dropIfExists('hotels');
    }
};

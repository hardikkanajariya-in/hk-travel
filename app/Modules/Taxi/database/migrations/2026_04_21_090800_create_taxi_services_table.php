<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxi_services', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('service_type')->index(); // airport_transfer, hourly, point_to_point
            $table->string('vehicle_type')->index();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('features')->nullable();
            $table->json('service_areas')->nullable();
            $table->unsignedSmallInteger('capacity')->default(4);
            $table->unsignedSmallInteger('luggage')->default(2);
            $table->decimal('base_fare', 10, 2)->default(0);
            $table->decimal('per_km_rate', 8, 2)->default(0);
            $table->decimal('per_hour_rate', 8, 2)->default(0);
            $table->decimal('flat_rate', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->json('seo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxi_services');
    }
};

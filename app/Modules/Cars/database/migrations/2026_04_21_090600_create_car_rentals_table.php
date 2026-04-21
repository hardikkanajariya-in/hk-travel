<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_rentals', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('vehicle_class')->index();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->json('features')->nullable();
            $table->json('pickup_locations')->nullable();
            $table->unsignedSmallInteger('seats')->default(5);
            $table->unsignedSmallInteger('doors')->default(4);
            $table->unsignedSmallInteger('luggage')->default(2);
            $table->string('transmission')->default('automatic');
            $table->string('fuel_type')->default('petrol');
            $table->boolean('has_ac')->default(true);
            $table->decimal('daily_rate', 10, 2)->default(0);
            $table->decimal('weekly_rate', 10, 2)->default(0);
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
        Schema::dropIfExists('car_rentals');
    }
};

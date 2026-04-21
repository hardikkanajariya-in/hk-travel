<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_routes', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('operator');
            $table->string('bus_type')->default('standard')->index();
            $table->string('origin')->index();
            $table->string('destination')->index();
            $table->json('stops')->nullable();
            $table->string('departure_time')->default('08:00');
            $table->string('arrival_time')->default('14:00');
            $table->unsignedSmallInteger('duration_minutes')->default(360);
            $table->unsignedInteger('distance_km')->default(0);
            $table->json('schedule_days')->nullable();
            $table->json('amenities')->nullable();
            $table->text('description')->nullable();
            $table->decimal('fare', 10, 2)->default(0);
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
        Schema::dropIfExists('bus_routes');
    }
};

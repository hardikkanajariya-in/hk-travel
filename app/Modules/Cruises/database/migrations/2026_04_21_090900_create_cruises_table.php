<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cruises', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('cruise_line');
            $table->string('ship_name')->nullable();
            $table->string('departure_port');
            $table->string('arrival_port');
            $table->date('departure_date')->nullable();
            $table->date('return_date')->nullable();
            $table->unsignedSmallInteger('duration_nights')->default(7);
            $table->text('description')->nullable();
            $table->text('highlights')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->json('itinerary')->nullable();
            $table->json('cabin_types')->nullable();
            $table->json('inclusions')->nullable();
            $table->json('exclusions')->nullable();
            $table->decimal('price_from', 10, 2)->default(0);
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
        Schema::dropIfExists('cruises');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_offers', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('airline');
            $table->string('airline_code', 4);
            $table->string('flight_number', 12);
            $table->string('origin', 4)->index();
            $table->string('destination', 4)->index();
            $table->string('depart_time');
            $table->string('arrive_time');
            $table->unsignedSmallInteger('duration_minutes')->default(0);
            $table->unsignedTinyInteger('stops')->default(0);
            $table->string('cabin', 20)->default('economy');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->json('segments')->nullable();
            $table->boolean('is_published')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['origin', 'destination']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_offers');
    }
};

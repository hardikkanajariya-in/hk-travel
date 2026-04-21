<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('train_offers', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('operator');
            $table->string('operator_code', 8);
            $table->string('train_number', 16);
            $table->string('origin', 8)->index();
            $table->string('destination', 8)->index();
            $table->string('depart_time');
            $table->string('arrive_time');
            $table->unsignedSmallInteger('duration_minutes')->default(0);
            $table->unsignedTinyInteger('changes')->default(0);
            $table->string('class', 20)->default('standard');
            $table->string('fare_type', 32)->nullable();
            $table->boolean('refundable')->default(false);
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
        Schema::dropIfExists('train_offers');
    }
};

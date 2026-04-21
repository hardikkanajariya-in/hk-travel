<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_services', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('country')->index();
            $table->string('country_code', 2)->nullable()->index();
            $table->string('visa_type')->index();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('eligibility')->nullable();
            $table->text('notes')->nullable();
            $table->json('requirements')->nullable();
            $table->json('documents')->nullable();
            $table->unsignedSmallInteger('processing_days_min')->default(7);
            $table->unsignedSmallInteger('processing_days_max')->default(21);
            $table->unsignedInteger('allowed_stay_days')->default(30);
            $table->unsignedInteger('validity_days')->default(180);
            $table->decimal('fee', 10, 2)->default(0);
            $table->decimal('service_fee', 10, 2)->default(0);
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
        Schema::dropIfExists('visa_services');
    }
};

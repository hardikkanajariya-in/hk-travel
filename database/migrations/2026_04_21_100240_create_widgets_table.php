<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('widgets', function (Blueprint $table): void {
            $table->id();
            $table->string('zone', 64);          // footer-1 | sidebar | …
            $table->string('type', 64);          // block type key
            $table->json('data')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['zone', 'is_active', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widgets');
    }
};

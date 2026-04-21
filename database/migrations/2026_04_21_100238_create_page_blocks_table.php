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
        Schema::create('page_blocks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->string('type', 64);
            $table->json('data')->nullable();
            $table->boolean('visible_mobile')->default(true);
            $table->boolean('visible_tablet')->default(true);
            $table->boolean('visible_desktop')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['page_id', 'position']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_blocks');
    }
};

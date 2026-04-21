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
        Schema::create('menu_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('target', 16)->default('_self');
            $table->string('icon')->nullable();
            $table->string('css_class')->nullable();
            $table->string('link_type', 32)->default('custom'); // custom | route | page | permalink
            $table->json('link_target')->nullable();
            $table->json('translations')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['menu_id', 'parent_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};

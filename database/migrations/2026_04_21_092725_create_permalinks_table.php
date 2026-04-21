<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permalinks', function (Blueprint $table): void {
            $table->id();
            $table->string('entity_type', 64);   // tour | hotel | page | blog_post | …
            $table->string('pattern');           // e.g. /tours/{slug}
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['entity_type', 'pattern']);
            $table->index(['entity_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permalinks');
    }
};

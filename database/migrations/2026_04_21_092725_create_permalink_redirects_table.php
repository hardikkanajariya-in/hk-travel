<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permalink_redirects', function (Blueprint $table): void {
            $table->id();
            $table->string('from_path');
            $table->string('to_path');
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('hit_count')->default(0);
            $table->timestamp('last_hit_at')->nullable();
            $table->timestamps();

            $table->unique(['from_path']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permalink_redirects');
    }
};

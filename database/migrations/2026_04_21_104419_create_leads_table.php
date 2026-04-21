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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pipeline_id')->nullable()->constrained('pipelines')->nullOnDelete();
            $table->string('stage', 64)->default('new')->index();
            $table->string('status', 16)->default('open')->index(); // open, won, lost
            $table->string('name');
            $table->string('email')->nullable()->index();
            $table->string('phone', 64)->nullable();
            $table->string('company')->nullable();
            $table->string('subject')->nullable();
            $table->string('source', 120)->nullable()->index();
            $table->decimal('value', 12, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('lost_reason')->nullable();
            $table->timestamp('won_at')->nullable();
            $table->timestamp('lost_at')->nullable();
            $table->timestamp('last_contacted_at')->nullable();
            $table->json('data')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();
            $table->index(['pipeline_id', 'stage', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

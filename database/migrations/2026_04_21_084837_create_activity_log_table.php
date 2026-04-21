<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable()->index();
            $table->text('description');
            // Subjects can be ULID-keyed (modules) or integer-keyed (core models),
            // so use a string column wide enough for either.
            $table->string('subject_id', 36)->nullable();
            $table->string('subject_type')->nullable();
            $table->index(['subject_id', 'subject_type'], 'subject');
            $table->string('event')->nullable();
            $table->string('causer_id', 36)->nullable();
            $table->string('causer_type')->nullable();
            $table->index(['causer_id', 'causer_type'], 'causer');
            $table->json('attribute_changes')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
        });
    }
};

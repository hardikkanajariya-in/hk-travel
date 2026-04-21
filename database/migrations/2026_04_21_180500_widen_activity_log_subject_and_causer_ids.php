<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Widen subject_id / causer_id on activity_log so it can store both
     * ULID (26 chars) and integer primary keys side by side. The original
     * migration used unsignedBigInteger which truncates ULIDs.
     */
    public function up(): void
    {
        if (! Schema::hasTable('activity_log')) {
            return;
        }

        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex('subject');
            $table->dropIndex('causer');
        });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->string('subject_id', 36)->nullable()->change();
            $table->string('causer_id', 36)->nullable()->change();
        });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->index(['subject_id', 'subject_type'], 'subject');
            $table->index(['causer_id', 'causer_type'], 'causer');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('activity_log')) {
            return;
        }

        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex('subject');
            $table->dropIndex('causer');
        });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable()->change();
            $table->unsignedBigInteger('causer_id')->nullable()->change();
        });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->index(['subject_id', 'subject_type'], 'subject');
            $table->index(['causer_id', 'causer_type'], 'causer');
        });
    }
};

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
        Schema::create('hk_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('group')->index();      // e.g. brand, seo, payments.stripe
            $table->string('key');                 // dotted path within the group
            $table->longText('value')->nullable(); // JSON-encoded payload
            $table->string('type', 32)->default('string'); // string|int|bool|array|json
            $table->boolean('is_public')->default(false);  // surfaced to frontend?
            $table->timestamps();

            $table->unique(['group', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hk_settings');
    }
};

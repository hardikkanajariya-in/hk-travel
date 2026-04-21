<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tours')) {
            return;
        }

        // Snapshot current values, then convert string columns to JSON shape.
        $rows = DB::table('tours')->get(['id', 'name', 'description']);

        $defaultLocale = (string) (config('hk.localization.default') ?? 'en');

        Schema::table('tours', function (Blueprint $table): void {
            $table->json('name_json')->nullable()->after('name');
            $table->json('description_json')->nullable()->after('description');
        });

        foreach ($rows as $row) {
            DB::table('tours')->where('id', $row->id)->update([
                'name_json' => json_encode([$defaultLocale => $row->name], JSON_UNESCAPED_UNICODE),
                'description_json' => json_encode([$defaultLocale => $row->description], JSON_UNESCAPED_UNICODE),
            ]);
        }

        Schema::table('tours', function (Blueprint $table): void {
            $table->dropColumn(['name', 'description']);
        });

        Schema::table('tours', function (Blueprint $table): void {
            $table->renameColumn('name_json', 'name');
            $table->renameColumn('description_json', 'description');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('tours')) {
            return;
        }

        Schema::table('tours', function (Blueprint $table): void {
            $table->renameColumn('name', 'name_json');
            $table->renameColumn('description', 'description_json');
        });

        Schema::table('tours', function (Blueprint $table): void {
            $table->string('name')->after('id');
            $table->text('description')->nullable()->after('slug');
        });

        $rows = DB::table('tours')->get(['id', 'name_json', 'description_json']);
        $defaultLocale = (string) (config('hk.localization.default') ?? 'en');

        foreach ($rows as $row) {
            $name = json_decode((string) $row->name_json, true);
            $desc = json_decode((string) $row->description_json, true);
            DB::table('tours')->where('id', $row->id)->update([
                'name' => is_array($name) ? ($name[$defaultLocale] ?? reset($name) ?: '') : '',
                'description' => is_array($desc) ? ($desc[$defaultLocale] ?? reset($desc) ?: null) : null,
            ]);
        }

        Schema::table('tours', function (Blueprint $table): void {
            $table->dropColumn(['name_json', 'description_json']);
        });
    }
};

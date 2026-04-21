<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{
    public function run(): void
    {
        $activeByDefault = ['en', 'hi', 'gu'];

        $languages = [
            ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'flag' => 'gb', 'is_rtl' => false, 'is_default' => true, 'sort_order' => 0],
            ['code' => 'hi', 'name' => 'Hindi', 'native_name' => 'हिन्दी', 'flag' => 'in', 'is_rtl' => false, 'sort_order' => 10],
            ['code' => 'gu', 'name' => 'Gujarati', 'native_name' => 'ગુજરાતી', 'flag' => 'in', 'is_rtl' => false, 'sort_order' => 20],
            ['code' => 'es', 'name' => 'Spanish', 'native_name' => 'Español', 'flag' => 'es', 'is_rtl' => false, 'sort_order' => 30],
            ['code' => 'fr', 'name' => 'French', 'native_name' => 'Français', 'flag' => 'fr', 'is_rtl' => false, 'sort_order' => 40],
            ['code' => 'de', 'name' => 'German', 'native_name' => 'Deutsch', 'flag' => 'de', 'is_rtl' => false, 'sort_order' => 50],
            ['code' => 'ar', 'name' => 'Arabic', 'native_name' => 'العربية', 'flag' => 'sa', 'is_rtl' => true, 'sort_order' => 60],
            ['code' => 'zh', 'name' => 'Chinese', 'native_name' => '中文', 'flag' => 'cn', 'is_rtl' => false, 'sort_order' => 70],
            ['code' => 'ja', 'name' => 'Japanese', 'native_name' => '日本語', 'flag' => 'jp', 'is_rtl' => false, 'sort_order' => 80],
        ];

        foreach ($languages as $lang) {
            $existing = Language::where('code', $lang['code'])->first();

            if ($existing) {
                // Don't trample manual is_active toggles on re-seed; only refresh
                // descriptive fields.
                $existing->fill([
                    'name' => $lang['name'],
                    'native_name' => $lang['native_name'],
                    'flag' => $lang['flag'],
                    'is_rtl' => $lang['is_rtl'],
                    'sort_order' => $lang['sort_order'],
                ])->save();

                // Activate hi/gu/en on first seed of these codes only if they
                // were previously inactive *and* are in our default-on list.
                if (! $existing->is_active && in_array($lang['code'], $activeByDefault, true)) {
                    $existing->forceFill(['is_active' => true])->save();
                }

                continue;
            }

            Language::create(array_merge([
                'is_active' => in_array($lang['code'], $activeByDefault, true),
                'is_default' => $lang['is_default'] ?? false,
            ], $lang));
        }
    }
}

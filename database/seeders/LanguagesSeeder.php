<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'flag' => 'gb', 'is_rtl' => false, 'is_default' => true, 'sort_order' => 0],
            ['code' => 'es', 'name' => 'Spanish', 'native_name' => 'Español', 'flag' => 'es', 'is_rtl' => false, 'sort_order' => 10],
            ['code' => 'fr', 'name' => 'French', 'native_name' => 'Français', 'flag' => 'fr', 'is_rtl' => false, 'sort_order' => 20],
            ['code' => 'de', 'name' => 'German', 'native_name' => 'Deutsch', 'flag' => 'de', 'is_rtl' => false, 'sort_order' => 30],
            ['code' => 'ar', 'name' => 'Arabic', 'native_name' => 'العربية', 'flag' => 'sa', 'is_rtl' => true, 'sort_order' => 40],
            ['code' => 'hi', 'name' => 'Hindi', 'native_name' => 'हिन्दी', 'flag' => 'in', 'is_rtl' => false, 'sort_order' => 50],
            ['code' => 'zh', 'name' => 'Chinese', 'native_name' => '中文', 'flag' => 'cn', 'is_rtl' => false, 'sort_order' => 60],
            ['code' => 'ja', 'name' => 'Japanese', 'native_name' => '日本語', 'flag' => 'jp', 'is_rtl' => false, 'sort_order' => 70],
        ];

        foreach ($languages as $lang) {
            Language::updateOrCreate(['code' => $lang['code']], array_merge(['is_active' => $lang['code'] === 'en'], $lang));
        }
    }
}

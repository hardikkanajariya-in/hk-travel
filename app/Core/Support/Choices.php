<?php

namespace App\Core\Support;

use DateTimeZone;
use Illuminate\Support\Carbon;

/**
 * Centralised "choice list" helper for admin forms.
 *
 * Every method returns an associative array of `value => human label` ready to
 * be passed to `<x-ui.select :options="...">`. Labels are written for
 * non-technical users — no jargon, no abbreviations like "ISO 4217".
 */
class Choices
{
    /**
     * Common languages the system supports out of the box.
     *
     * @return array<string, string>
     */
    public static function locales(): array
    {
        return [
            'en' => 'English',
            'en-GB' => 'English (United Kingdom)',
            'en-US' => 'English (United States)',
            'hi' => 'Hindi (हिन्दी)',
            'gu' => 'Gujarati (ગુજરાતી)',
            'ar' => 'Arabic (العربية)',
            'fr' => 'French (Français)',
            'es' => 'Spanish (Español)',
            'de' => 'German (Deutsch)',
            'it' => 'Italian (Italiano)',
            'pt' => 'Portuguese (Português)',
            'ru' => 'Russian (Русский)',
            'zh' => 'Chinese (中文)',
            'ja' => 'Japanese (日本語)',
            'ko' => 'Korean (한국어)',
            'tr' => 'Turkish (Türkçe)',
            'nl' => 'Dutch (Nederlands)',
            'pl' => 'Polish (Polski)',
            'th' => 'Thai (ไทย)',
            'vi' => 'Vietnamese (Tiếng Việt)',
            'id' => 'Indonesian (Bahasa Indonesia)',
            'ms' => 'Malay (Bahasa Melayu)',
            'fa' => 'Persian (فارسی)',
            'he' => 'Hebrew (עברית)',
            'ur' => 'Urdu (اُردُو)',
        ];
    }

    /**
     * Locales that read right-to-left.
     *
     * @return array<int, string>
     */
    public static function rtlLocales(): array
    {
        return ['ar', 'he', 'fa', 'ur'];
    }

    /**
     * Major world currencies.
     *
     * @return array<string, string>
     */
    public static function currencies(): array
    {
        return [
            'USD' => 'US Dollar ($)',
            'EUR' => 'Euro (€)',
            'GBP' => 'British Pound (£)',
            'INR' => 'Indian Rupee (₹)',
            'AED' => 'UAE Dirham (د.إ)',
            'AUD' => 'Australian Dollar (A$)',
            'CAD' => 'Canadian Dollar (C$)',
            'CHF' => 'Swiss Franc (CHF)',
            'CNY' => 'Chinese Yuan (¥)',
            'JPY' => 'Japanese Yen (¥)',
            'SGD' => 'Singapore Dollar (S$)',
            'HKD' => 'Hong Kong Dollar (HK$)',
            'NZD' => 'New Zealand Dollar (NZ$)',
            'ZAR' => 'South African Rand (R)',
            'BRL' => 'Brazilian Real (R$)',
            'MXN' => 'Mexican Peso (MX$)',
            'SAR' => 'Saudi Riyal (﷼)',
            'TRY' => 'Turkish Lira (₺)',
            'THB' => 'Thai Baht (฿)',
            'KRW' => 'South Korean Won (₩)',
            'MYR' => 'Malaysian Ringgit (RM)',
            'IDR' => 'Indonesian Rupiah (Rp)',
            'PHP' => 'Philippine Peso (₱)',
            'EGP' => 'Egyptian Pound (E£)',
            'NPR' => 'Nepalese Rupee (Rs)',
            'LKR' => 'Sri Lankan Rupee (Rs)',
            'BDT' => 'Bangladeshi Taka (৳)',
            'PKR' => 'Pakistani Rupee (Rs)',
            'KWD' => 'Kuwaiti Dinar (د.ك)',
            'OMR' => 'Omani Rial (﷼)',
            'QAR' => 'Qatari Riyal (﷼)',
            'BHD' => 'Bahraini Dinar (د.ب)',
        ];
    }

    /**
     * Timezones grouped by continent for easier scanning.
     *
     * @return array<string, string>
     */
    public static function timezones(): array
    {
        $zones = DateTimeZone::listIdentifiers();
        $now = Carbon::now();
        $list = [];

        foreach ($zones as $zone) {
            try {
                $offset = $now->copy()->setTimezone($zone)->format('P');
                $label = str_replace('_', ' ', $zone)." (UTC{$offset})";
                $list[$zone] = $label;
            } catch (\Throwable) {
                $list[$zone] = $zone;
            }
        }

        return $list;
    }

    /**
     * Friendly date format presets with live previews.
     *
     * @return array<string, string>
     */
    public static function dateFormats(): array
    {
        $now = Carbon::now();
        $formats = [
            'Y-m-d' => '2026-04-21',
            'd/m/Y' => '21/04/2026',
            'm/d/Y' => '04/21/2026',
            'd-m-Y' => '21-04-2026',
            'd.m.Y' => '21.04.2026',
            'd M Y' => '21 Apr 2026',
            'M d, Y' => 'Apr 21, 2026',
            'D, d M Y' => 'Tue, 21 Apr 2026',
            'F j, Y' => 'April 21, 2026',
            'jS F Y' => '21st April 2026',
        ];

        $list = [];
        foreach ($formats as $fmt => $_example) {
            $list[$fmt] = $now->format($fmt);
        }

        return $list;
    }

    /**
     * Friendly time format presets with live previews.
     *
     * @return array<string, string>
     */
    public static function timeFormats(): array
    {
        $now = Carbon::now();

        return [
            'H:i' => $now->format('H:i').' (24-hour)',
            'H:i:s' => $now->format('H:i:s').' (24-hour with seconds)',
            'g:i a' => $now->format('g:i a').' (12-hour, lowercase)',
            'g:i A' => $now->format('g:i A').' (12-hour, uppercase)',
            'g:i:s A' => $now->format('g:i:s A').' (12-hour with seconds)',
        ];
    }

    /**
     * Search engine indexing presets.
     *
     * @return array<string, string>
     */
    public static function robotsDirectives(): array
    {
        return [
            'index, follow' => 'Show in search engines (recommended)',
            'noindex, follow' => 'Hide from search engines, but follow links',
            'index, nofollow' => 'Show in search engines, do not follow links',
            'noindex, nofollow' => 'Hide from search engines completely',
        ];
    }

    /**
     * Cookie banner positions on the page.
     *
     * @return array<string, string>
     */
    public static function cookiePositions(): array
    {
        return [
            'bottom' => 'Bottom of the page (full width)',
            'top' => 'Top of the page (full width)',
            'bottom-left' => 'Bottom-left corner card',
            'bottom-right' => 'Bottom-right corner card',
        ];
    }

    /**
     * SEO title separator characters.
     *
     * @return array<string, string>
     */
    public static function titleSeparators(): array
    {
        return [
            '·' => 'Middle dot  (·)   — Page · Site',
            '|' => 'Vertical bar (|)  — Page | Site',
            '-' => 'Hyphen (-)        — Page - Site',
            '–' => 'En dash (–)       — Page – Site',
            '—' => 'Em dash (—)       — Page — Site',
            '/' => 'Slash (/)         — Page / Site',
            '»' => 'Right arrow (»)   — Page » Site',
        ];
    }

    /**
     * Flight search data sources.
     *
     * @return array<string, string>
     */
    public static function flightProviders(): array
    {
        return [
            'stub' => 'Saved offers only (recommended while setting up)',
            'amadeus' => 'Amadeus live flight results',
            'duffel' => 'Duffel live flight results',
        ];
    }

    /**
     * Train search data sources.
     *
     * @return array<string, string>
     */
    public static function trainProviders(): array
    {
        return [
            'stub' => 'Saved offers only (recommended while setting up)',
            'sabre' => 'Sabre Rail live train results',
            'trainline' => 'Trainline live train results',
        ];
    }

    /**
     * Review sort order labels.
     *
     * @return array<string, string>
     */
    public static function reviewSortOptions(): array
    {
        return [
            'newest' => 'Newest first',
            'rating_desc' => 'Highest rating first',
            'rating_asc' => 'Lowest rating first',
            'helpful' => 'Most helpful first',
        ];
    }

    /**
     * Review score labels.
     *
     * @return array<int, string>
     */
    public static function reviewScoreOptions(): array
    {
        return [
            5 => '5 out of 5',
            4 => '4 out of 5',
            3 => '3 out of 5',
            2 => '2 out of 5',
            1 => '1 out of 5',
        ];
    }

    /**
     * Moderation status labels.
     *
     * @return array<string, string>
     */
    public static function moderationStatuses(): array
    {
        return [
            'pending' => 'Waiting for review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'spam' => 'Spam',
        ];
    }

    /**
     * Blog publishing status labels.
     *
     * @return array<string, string>
     */
    public static function blogPostStatuses(): array
    {
        return [
            'draft' => 'Draft',
            'scheduled' => 'Scheduled for later',
            'published' => 'Published',
            'archived' => 'Archived',
        ];
    }

    /**
     * Country codes for flag selection (ISO 3166-1 alpha-2, lowercase).
     *
     * @return array<string, string>
     */
    public static function countryFlags(): array
    {
        return [
            'gb' => '🇬🇧  United Kingdom',
            'us' => '🇺🇸  United States',
            'in' => '🇮🇳  India',
            'ae' => '🇦🇪  United Arab Emirates',
            'sa' => '🇸🇦  Saudi Arabia',
            'fr' => '🇫🇷  France',
            'de' => '🇩🇪  Germany',
            'es' => '🇪🇸  Spain',
            'it' => '🇮🇹  Italy',
            'pt' => '🇵🇹  Portugal',
            'br' => '🇧🇷  Brazil',
            'mx' => '🇲🇽  Mexico',
            'ca' => '🇨🇦  Canada',
            'au' => '🇦🇺  Australia',
            'nz' => '🇳🇿  New Zealand',
            'jp' => '🇯🇵  Japan',
            'cn' => '🇨🇳  China',
            'kr' => '🇰🇷  South Korea',
            'sg' => '🇸🇬  Singapore',
            'hk' => '🇭🇰  Hong Kong',
            'my' => '🇲🇾  Malaysia',
            'id' => '🇮🇩  Indonesia',
            'ph' => '🇵🇭  Philippines',
            'th' => '🇹🇭  Thailand',
            'vn' => '🇻🇳  Vietnam',
            'tr' => '🇹🇷  Türkiye',
            'eg' => '🇪🇬  Egypt',
            'za' => '🇿🇦  South Africa',
            'ru' => '🇷🇺  Russia',
            'pl' => '🇵🇱  Poland',
            'nl' => '🇳🇱  Netherlands',
            'ch' => '🇨🇭  Switzerland',
            'ie' => '🇮🇪  Ireland',
            'np' => '🇳🇵  Nepal',
            'lk' => '🇱🇰  Sri Lanka',
            'bd' => '🇧🇩  Bangladesh',
            'pk' => '🇵🇰  Pakistan',
            'ir' => '🇮🇷  Iran',
            'il' => '🇮🇱  Israel',
            'qa' => '🇶🇦  Qatar',
            'kw' => '🇰🇼  Kuwait',
            'om' => '🇴🇲  Oman',
            'bh' => '🇧🇭  Bahrain',
        ];
    }

    /**
     * "How long browsers should remember to use HTTPS" presets in seconds.
     *
     * @return array<int, string>
     */
    public static function hstsDurations(): array
    {
        return [
            0 => 'Off (do not set)',
            300 => '5 minutes (testing only)',
            86400 => '1 day',
            604800 => '1 week',
            2592000 => '1 month',
            15768000 => '6 months',
            31536000 => '1 year (recommended)',
            63072000 => '2 years (HSTS preload eligible)',
        ];
    }

    /**
     * Browser referrer privacy presets.
     *
     * @return array<string, string>
     */
    public static function referrerPolicies(): array
    {
        return [
            'no-referrer' => 'Never share where visitors came from (most private)',
            'no-referrer-when-downgrade' => 'Share, except when going from HTTPS to HTTP',
            'origin' => 'Share only the website name, not the full link',
            'origin-when-cross-origin' => 'Share full link on same site, only website name elsewhere',
            'same-origin' => 'Share only with the same website',
            'strict-origin' => 'Share website name only, and only over HTTPS',
            'strict-origin-when-cross-origin' => 'Balanced default (recommended)',
            'unsafe-url' => 'Always share the full link (least private)',
        ];
    }

    /**
     * Permissions-Policy presets — these are pre-built strings rather than
     * exposing the raw header syntax.
     *
     * @return array<string, string>
     */
    public static function permissionsPolicies(): array
    {
        return [
            'camera=(), microphone=(), geolocation=(), payment=()' => 'Block camera, microphone, location & payment APIs (recommended)',
            'camera=(), microphone=(), geolocation=()' => 'Block camera, microphone & location (allow payment)',
            'camera=(), microphone=()' => 'Block camera & microphone only',
            '' => 'Allow everything (not recommended)',
        ];
    }

    /**
     * reCAPTCHA strictness presets — friendly labels for the score threshold.
     *
     * @return array<string, string>
     */
    public static function recaptchaSensitivities(): array
    {
        return [
            '0.3' => 'Lenient (0.3) — fewer false positives, more bots through',
            '0.5' => 'Balanced (0.5) — recommended default',
            '0.7' => 'Strict (0.7) — fewer bots, may block real users',
            '0.9' => 'Very strict (0.9) — most aggressive',
        ];
    }
}

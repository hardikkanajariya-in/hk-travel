<?php

namespace App\Http\Controllers;

use App\Core\Settings\SettingsRepository;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(SettingsRepository $settings): Response
    {
        $custom = (string) $settings->get('seo.robots_txt', '');

        if (trim($custom) !== '') {
            $body = $custom;
        } else {
            $body = $settings->get('seo.noindex_site', false)
                ? "User-agent: *\nDisallow: /\n"
                : "User-agent: *\nDisallow: /admin\nDisallow: /dashboard\nDisallow: /livewire\n\nSitemap: ".url('/sitemap.xml')."\n";
        }

        return response($body, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}

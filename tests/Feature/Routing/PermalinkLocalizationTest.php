<?php

namespace Tests\Feature\Routing;

use App\Core\Localization\LocaleManager;
use App\Core\Permalink\PermalinkRouter;
use App\Core\Routing\PublicUrlGenerator;
use App\Modules\Tours\Models\Tour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermalinkLocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_urls_keep_default_locale_unprefixed_and_prefix_secondary_locales(): void
    {
        $urls = app(PublicUrlGenerator::class);

        $this->assertSame(url('/tours/sunrise-in-jaipur'), $urls->entity('tour', ['slug' => 'sunrise-in-jaipur'], 'en'));
        $this->assertSame(url('/hi/tours/sunrise-in-jaipur'), $urls->entity('tour', ['slug' => 'sunrise-in-jaipur'], 'hi'));
    }

    public function test_saving_a_new_permalink_pattern_creates_a_legacy_redirect_from_the_old_path(): void
    {
        $router = app(PermalinkRouter::class);

        $router->set('tour', '/journeys/{slug}');

        $this->assertDatabaseHas('permalinks', [
            'entity_type' => 'tour',
            'pattern' => '/journeys/{slug}',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('permalink_redirects', [
            'from_path' => '/tours/{slug}',
            'to_path' => '/journeys/{slug}',
            'status_code' => 301,
            'is_active' => true,
        ]);
    }

    public function test_locale_prefixed_homepage_routes_are_available(): void
    {
        $response = $this->get('/hi');

        $response->assertOk();
        $this->assertSame('hi', app(LocaleManager::class)->detect(request()->create('/hi', 'GET')));
    }

    public function test_tour_routes_honor_custom_permalink_patterns_and_keep_the_default_path_as_a_legacy_redirect(): void
    {
        $tour = Tour::factory()->create([
            'slug' => 'sunrise-in-jaipur',
            'name' => 'Sunrise in Jaipur',
            'is_published' => true,
        ]);

        app(PermalinkRouter::class)->set('tour', '/journeys/{slug}');

        $this->get('/journeys/'.$tour->slug)->assertOk();
        $this->get('/tours/'.$tour->slug)->assertRedirect('/journeys/'.$tour->slug);
    }

    public function test_locale_prefixed_module_routes_are_available(): void
    {
        $response = $this->get('/hi/tours');

        $response->assertOk();
    }
}

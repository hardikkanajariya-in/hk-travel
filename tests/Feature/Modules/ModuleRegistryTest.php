<?php

namespace Tests\Feature\Modules;

use App\Core\Settings\SettingsRepository;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleRegistryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_modules_screen_hides_future_modules_without_manifests(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $response = $this->actingAs($user)->get(route('admin.modules'));

        $response->assertOk();
        $response->assertSee('CRM &amp; Leads', false);
        $response->assertDontSee('Bookings');
        $response->assertDontSee('Travel Packages');
    }

    public function test_disabling_crm_module_makes_crm_admin_routes_unavailable(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        app(SettingsRepository::class)->set('modules.crm.enabled', false);

        $response = $this->actingAs($user)->get('/admin/crm/leads');

        $response->assertNotFound();
    }

    public function test_settings_page_includes_transport_provider_controls(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $response = $this->actingAs($user)->get(route('admin.settings'));

        $response->assertOk();
        $response->assertSee('Transport providers');
        $response->assertSee('Flight source');
        $response->assertSee('Train source');
    }
}

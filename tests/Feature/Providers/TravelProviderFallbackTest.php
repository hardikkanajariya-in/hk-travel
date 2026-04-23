<?php

namespace Tests\Feature\Providers;

use App\Core\Settings\SettingsRepository;
use App\Modules\Flights\Contracts\FlightProvider;
use App\Modules\Flights\DTO\FlightSearchCriteria;
use App\Modules\Trains\Contracts\TrainProvider;
use App\Modules\Trains\DTO\TrainSearchCriteria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelProviderFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_flights_fall_back_to_stub_when_selected_provider_is_not_configured(): void
    {
        config()->set('hk-modules.modules.flights.provider', 'amadeus');
        config()->set('hk-modules.modules.flights.amadeus', []);

        $provider = app(FlightProvider::class);
        $results = $provider->search(new FlightSearchCriteria(
            origin: 'LHR',
            destination: 'JFK',
            departDate: now()->addWeek()->toDateString(),
        ));

        $this->assertNotEmpty($results);
        $this->assertSame('stub', $results->first()->provider);
    }

    public function test_trains_fall_back_to_stub_when_selected_provider_is_not_configured(): void
    {
        config()->set('hk-modules.modules.trains.provider', 'trainline');
        config()->set('hk-modules.modules.trains.trainline', []);

        $provider = app(TrainProvider::class);
        $results = $provider->search(new TrainSearchCriteria(
            origin: 'LON',
            destination: 'EDI',
            departDate: now()->addWeek()->toDateString(),
        ));

        $this->assertNotEmpty($results);
        $this->assertSame('stub', $results->first()->provider);
    }

    public function test_flight_provider_selection_can_be_driven_from_saved_settings(): void
    {
        app(SettingsRepository::class)->setMany([
            'modules.flights.provider' => 'amadeus',
            'modules.flights.amadeus.api_key' => 'demo-client',
            'modules.flights.amadeus.api_secret' => 'demo-secret',
            'modules.flights.amadeus.base_url' => 'https://test.api.amadeus.com',
        ]);

        $this->app->forgetInstance(FlightProvider::class);
        $provider = app(FlightProvider::class);

        $this->assertSame('amadeus', $provider->name());
    }

    public function test_train_provider_selection_can_be_driven_from_saved_settings(): void
    {
        app(SettingsRepository::class)->setMany([
            'modules.trains.provider' => 'trainline',
            'modules.trains.trainline.api_key' => 'demo-token',
            'modules.trains.trainline.base_url' => 'https://partner-api.thetrainline.com',
        ]);

        $this->app->forgetInstance(TrainProvider::class);
        $provider = app(TrainProvider::class);

        $this->assertSame('trainline', $provider->name());
    }
}

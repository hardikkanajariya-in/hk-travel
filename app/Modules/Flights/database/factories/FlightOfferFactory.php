<?php

namespace App\Modules\Flights\Database\Factories;

use App\Modules\Flights\Models\FlightOffer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class FlightOfferFactory extends Factory
{
    protected $model = FlightOffer::class;

    public function definition(): array
    {
        $airlines = [['Sky Airways', 'SA'], ['Globe Airlines', 'GA'], ['Cloud Jet', 'CJ'], ['Atlas Air', 'AT']];
        [$airline, $code] = $this->faker->randomElement($airlines);
        $depart = Carbon::now()->addDays($this->faker->numberBetween(7, 60))->setTime($this->faker->numberBetween(5, 22), 0);
        $duration = $this->faker->numberBetween(60, 720);

        return [
            'airline' => $airline,
            'airline_code' => $code,
            'flight_number' => $code.$this->faker->numberBetween(100, 999),
            'origin' => strtoupper($this->faker->lexify('???')),
            'destination' => strtoupper($this->faker->lexify('???')),
            'depart_time' => $depart->toIso8601String(),
            'arrive_time' => $depart->copy()->addMinutes($duration)->toIso8601String(),
            'duration_minutes' => $duration,
            'stops' => $this->faker->numberBetween(0, 2),
            'cabin' => $this->faker->randomElement(['economy', 'premium_economy', 'business']),
            'price' => $this->faker->randomFloat(2, 80, 1500),
            'currency' => 'USD',
            'is_published' => true,
        ];
    }
}

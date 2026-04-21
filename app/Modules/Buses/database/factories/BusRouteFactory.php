<?php

namespace App\Modules\Buses\Database\Factories;

use App\Modules\Buses\Models\BusRoute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BusRouteFactory extends Factory
{
    protected $model = BusRoute::class;

    public function definition(): array
    {
        $origin = $this->faker->city();
        $dest = $this->faker->city();
        $title = "{$origin} → {$dest}";

        return [
            'title' => $title,
            'slug' => Str::slug("{$origin}-{$dest}").'-'.$this->faker->unique()->numberBetween(1, 99999),
            'operator' => $this->faker->company().' Coach',
            'bus_type' => $this->faker->randomElement(['standard', 'ac', 'sleeper', 'luxury']),
            'origin' => $origin,
            'destination' => $dest,
            'stops' => [$this->faker->city(), $this->faker->city()],
            'departure_time' => $this->faker->randomElement(['06:00', '08:30', '14:00', '20:00']),
            'arrival_time' => $this->faker->randomElement(['12:00', '14:30', '20:00', '06:00']),
            'duration_minutes' => $this->faker->numberBetween(120, 720),
            'distance_km' => $this->faker->numberBetween(50, 800),
            'schedule_days' => ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'],
            'amenities' => ['Wi-Fi', 'A/C', 'Reclining seats', 'USB charging'],
            'description' => $this->faker->paragraph(),
            'fare' => $this->faker->randomFloat(2, 5, 80),
            'currency' => 'USD',
            'is_published' => true,
        ];
    }
}

<?php

namespace App\Modules\Taxi\Database\Factories;

use App\Modules\Taxi\Models\TaxiService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TaxiServiceFactory extends Factory
{
    protected $model = TaxiService::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['airport_transfer', 'hourly', 'point_to_point']);
        $vehicle = $this->faker->randomElement(['Sedan', 'SUV', 'Van', 'Luxury', 'Electric']);
        $title = ucwords(str_replace('_', ' ', $type)).' - '.$vehicle;

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'service_type' => $type,
            'vehicle_type' => $vehicle,
            'description' => $this->faker->paragraphs(2, true),
            'features' => ['Bottled water', 'Wi-Fi', 'Phone charger', 'Meet & greet'],
            'service_areas' => [$this->faker->city(), $this->faker->city()],
            'capacity' => $this->faker->randomElement([3, 4, 6, 8]),
            'luggage' => $this->faker->numberBetween(1, 5),
            'base_fare' => $this->faker->randomFloat(2, 5, 30),
            'per_km_rate' => $this->faker->randomFloat(2, 0.8, 4),
            'per_hour_rate' => $this->faker->randomFloat(2, 20, 80),
            'flat_rate' => $this->faker->randomFloat(2, 25, 200),
            'currency' => 'USD',
            'is_published' => true,
        ];
    }
}

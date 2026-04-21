<?php

namespace App\Modules\Cars\Database\Factories;

use App\Modules\Cars\Models\CarRental;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CarRentalFactory extends Factory
{
    protected $model = CarRental::class;

    public function definition(): array
    {
        $make = $this->faker->randomElement(['Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes', 'Hyundai', 'Kia']);
        $model = $this->faker->randomElement(['Corolla', 'Civic', 'Focus', 'C-Class', 'Tucson', 'Sportage']);
        $name = "{$make} {$model}";

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'vehicle_class' => $this->faker->randomElement(['economy', 'compact', 'sedan', 'suv', 'luxury', 'van']),
            'make' => $make,
            'model' => $model,
            'description' => $this->faker->paragraphs(2, true),
            'features' => ['Bluetooth', 'USB charging', 'GPS', 'Cruise control'],
            'pickup_locations' => ['Airport', 'City centre'],
            'seats' => $this->faker->randomElement([4, 5, 7]),
            'doors' => $this->faker->randomElement([2, 4, 5]),
            'luggage' => $this->faker->numberBetween(1, 4),
            'transmission' => $this->faker->randomElement(['automatic', 'manual']),
            'fuel_type' => $this->faker->randomElement(['petrol', 'diesel', 'hybrid', 'electric']),
            'has_ac' => true,
            'daily_rate' => $this->faker->randomFloat(2, 25, 250),
            'weekly_rate' => $this->faker->randomFloat(2, 150, 1500),
            'currency' => 'USD',
            'is_published' => true,
        ];
    }
}

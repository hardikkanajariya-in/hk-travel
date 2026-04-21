<?php

namespace App\Modules\Cruises\Database\Factories;

use App\Modules\Cruises\Models\Cruise;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CruiseFactory extends Factory
{
    protected $model = Cruise::class;

    public function definition(): array
    {
        $title = ucwords($this->faker->words(3, true)).' Cruise';
        $nights = $this->faker->randomElement([3, 5, 7, 10, 14]);
        $depart = $this->faker->dateTimeBetween('+1 month', '+9 months');

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'cruise_line' => $this->faker->randomElement(['Royal Caribbean', 'Norwegian', 'MSC', 'Costa', 'Princess']),
            'ship_name' => 'MV '.$this->faker->lastName(),
            'departure_port' => $this->faker->city(),
            'arrival_port' => $this->faker->city(),
            'departure_date' => $depart,
            'return_date' => (clone $depart)->modify("+{$nights} days"),
            'duration_nights' => $nights,
            'description' => $this->faker->paragraphs(3, true),
            'highlights' => $this->faker->sentence(),
            'itinerary' => collect(range(1, $nights))->map(fn ($d) => [
                'day' => $d,
                'port' => $this->faker->city(),
                'activity' => $this->faker->sentence(),
            ])->all(),
            'cabin_types' => [
                ['name' => 'Interior', 'price' => $this->faker->numberBetween(400, 900), 'capacity' => 2],
                ['name' => 'Ocean View', 'price' => $this->faker->numberBetween(800, 1400), 'capacity' => 2],
                ['name' => 'Balcony', 'price' => $this->faker->numberBetween(1200, 2000), 'capacity' => 3],
                ['name' => 'Suite', 'price' => $this->faker->numberBetween(2500, 5000), 'capacity' => 4],
            ],
            'inclusions' => ['All meals', 'Entertainment', 'Pool access', 'Fitness centre'],
            'exclusions' => ['Drinks package', 'Shore excursions', 'Spa', 'Gratuities'],
            'price_from' => $this->faker->randomFloat(2, 399, 1500),
            'currency' => 'USD',
            'is_published' => true,
        ];
    }
}

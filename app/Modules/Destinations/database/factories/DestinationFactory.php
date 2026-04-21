<?php

namespace App\Modules\Destinations\Database\Factories;

use App\Modules\Destinations\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DestinationFactory extends Factory
{
    protected $model = Destination::class;

    public function definition(): array
    {
        $name = $this->faker->city();

        return [
            'type' => $this->faker->randomElement(['country', 'region', 'city', 'area']),
            'name' => $name,
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'country_code' => $this->faker->countryCode(),
            'description' => $this->faker->paragraphs(3, true),
            'highlights' => $this->faker->sentences(4, true),
            'cover_image' => null,
            'lat' => $this->faker->latitude(),
            'lng' => $this->faker->longitude(),
            'is_featured' => $this->faker->boolean(20),
            'is_published' => true,
        ];
    }

    public function unpublished(): self
    {
        return $this->state(['is_published' => false]);
    }
}

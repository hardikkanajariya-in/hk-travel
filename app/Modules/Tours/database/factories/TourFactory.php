<?php

namespace App\Modules\Tours\Database\Factories;

use App\Modules\Tours\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TourFactory extends Factory
{
    protected $model = Tour::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement(['Sunset', 'Mountain', 'Coastal', 'Cultural', 'Luxury', 'Adventure']).' '.
            $this->faker->randomElement(['Discovery', 'Escape', 'Journey', 'Expedition', 'Retreat']);

        return [
            'destination_id' => null,
            'name' => $name,
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'description' => $this->faker->paragraphs(4, true),
            'cover_image' => null,
            'gallery' => [],
            'inclusions' => ['Accommodation', 'Meals', 'Local guide', 'Transport'],
            'exclusions' => ['International flights', 'Personal expenses', 'Travel insurance'],
            'itinerary' => [],
            'difficulty' => $this->faker->randomElement(['easy', 'moderate', 'challenging']),
            'language' => 'en',
            'price' => $this->faker->randomFloat(2, 200, 5000),
            'discount_price' => null,
            'currency' => 'USD',
            'duration_days' => $this->faker->numberBetween(1, 21),
            'max_group_size' => $this->faker->numberBetween(4, 30),
            'is_published' => true,
            'is_featured' => $this->faker->boolean(20),
            'published_at' => now(),
        ];
    }
}

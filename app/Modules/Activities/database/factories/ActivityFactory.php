<?php

namespace App\Modules\Activities\Database\Factories;

use App\Modules\Activities\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement(['Sunset Cruise', 'City Walking Tour', 'Cooking Class', 'Snorkel Trip', 'Wine Tasting']).' '.$this->faker->city();

        return [
            'destination_id' => null,
            'name' => $name,
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'category' => $this->faker->randomElement(['adventure', 'culture', 'food', 'nature', 'wellness']),
            'short_description' => $this->faker->sentence(12),
            'description' => $this->faker->paragraphs(3, true),
            'cover_image' => null,
            'highlights' => $this->faker->sentences(4),
            'included' => ['Guide', 'Equipment', 'Refreshments'],
            'duration_hours' => $this->faker->randomFloat(1, 1, 8),
            'price' => $this->faker->randomFloat(2, 25, 250),
            'currency' => 'USD',
            'min_age' => $this->faker->randomElement([0, 6, 12, 18]),
            'max_group_size' => $this->faker->numberBetween(6, 30),
            'difficulty' => $this->faker->randomElement(['easy', 'moderate', 'challenging']),
            'is_published' => true,
            'is_featured' => $this->faker->boolean(20),
        ];
    }
}

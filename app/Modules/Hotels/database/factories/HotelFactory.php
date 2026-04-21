<?php

namespace App\Modules\Hotels\Database\Factories;

use App\Modules\Hotels\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class HotelFactory extends Factory
{
    protected $model = Hotel::class;

    public function definition(): array
    {
        $name = $this->faker->company().' Hotel';

        return [
            'destination_id' => null,
            'name' => $name,
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'star_rating' => $this->faker->numberBetween(2, 5),
            'description' => $this->faker->paragraphs(3, true),
            'cover_image' => null,
            'gallery' => [],
            'amenities' => $this->faker->randomElements(['Wi-Fi', 'Pool', 'Spa', 'Gym', 'Parking', 'Bar', 'Restaurant', 'Airport shuttle'], 4),
            'address' => $this->faker->address(),
            'lat' => $this->faker->latitude(),
            'lng' => $this->faker->longitude(),
            'check_in' => '15:00',
            'check_out' => '11:00',
            'price_from' => $this->faker->randomFloat(2, 50, 800),
            'currency' => 'USD',
            'is_published' => true,
            'is_featured' => $this->faker->boolean(20),
        ];
    }
}

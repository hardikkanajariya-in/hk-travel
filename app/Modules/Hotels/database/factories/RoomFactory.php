<?php

namespace App\Modules\Hotels\Database\Factories;

use App\Modules\Hotels\Models\Hotel;
use App\Modules\Hotels\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'name' => $this->faker->randomElement([
                'Standard Double', 'Deluxe Twin', 'Superior King', 'Family Suite',
                'Junior Suite', 'Executive Suite', 'Garden View', 'Sea View Premium',
            ]),
            'description' => $this->faker->paragraph(3),
            'price_per_night' => $this->faker->randomFloat(2, 60, 850),
            'capacity_adults' => $this->faker->numberBetween(1, 4),
            'capacity_children' => $this->faker->numberBetween(0, 2),
            'inventory' => $this->faker->numberBetween(1, 20),
            'is_available' => true,
            'amenities' => $this->faker->randomElements([
                'Air conditioning', 'Free WiFi', 'Mini bar', 'Safe', 'Television',
                'Coffee machine', 'Balcony', 'Bath tub', 'Hair dryer', 'Iron',
            ], $this->faker->numberBetween(3, 7)),
        ];
    }

    public function unavailable(): self
    {
        return $this->state(['is_available' => false]);
    }

    public function suite(): self
    {
        return $this->state([
            'name' => 'Executive Suite',
            'price_per_night' => $this->faker->randomFloat(2, 400, 1500),
            'capacity_adults' => 4,
            'capacity_children' => 2,
        ]);
    }
}

<?php

namespace App\Modules\Trains\Database\Factories;

use App\Modules\Trains\Models\TrainOffer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TrainOfferFactory extends Factory
{
    protected $model = TrainOffer::class;

    public function definition(): array
    {
        $operators = [
            ['Eurostar', 'EST'], ['SNCF', 'SNF'], ['Trenitalia', 'TRN'],
            ['Renfe', 'RNF'], ['Deutsche Bahn', 'DB'], ['Amtrak', 'AMT'],
        ];
        [$op, $code] = $this->faker->randomElement($operators);
        $depart = Carbon::now()->addDays($this->faker->numberBetween(3, 60))->setTime($this->faker->numberBetween(5, 22), 0);
        $duration = $this->faker->numberBetween(45, 600);

        return [
            'operator' => $op,
            'operator_code' => $code,
            'train_number' => $code.$this->faker->numberBetween(1000, 9999),
            'origin' => strtoupper($this->faker->lexify('???')),
            'destination' => strtoupper($this->faker->lexify('???')),
            'depart_time' => $depart->toIso8601String(),
            'arrive_time' => $depart->copy()->addMinutes($duration)->toIso8601String(),
            'duration_minutes' => $duration,
            'changes' => $this->faker->numberBetween(0, 2),
            'class' => $this->faker->randomElement(['standard', 'first', 'business']),
            'fare_type' => $this->faker->randomElement(['standard', 'flexible', 'advance']),
            'refundable' => $this->faker->boolean(40),
            'price' => $this->faker->randomFloat(2, 25, 450),
            'currency' => 'USD',
            'is_published' => true,
        ];
    }
}

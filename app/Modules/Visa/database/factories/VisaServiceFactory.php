<?php

namespace App\Modules\Visa\Database\Factories;

use App\Modules\Visa\Models\VisaService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VisaServiceFactory extends Factory
{
    protected $model = VisaService::class;

    public function definition(): array
    {
        $country = $this->faker->country();
        $type = $this->faker->randomElement(['Tourist', 'Business', 'Transit', 'Student', 'Work']);
        $title = "{$country} {$type} Visa";

        return [
            'country' => $country,
            'country_code' => strtoupper($this->faker->countryCode()),
            'visa_type' => $type,
            'title' => $title,
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'description' => $this->faker->paragraphs(2, true),
            'eligibility' => 'Valid passport with at least 6 months validity beyond travel.',
            'requirements' => ['Valid passport', 'Proof of accommodation', 'Return ticket', 'Bank statement'],
            'documents' => ['Passport scan', 'Photo', 'Itinerary', 'Hotel booking'],
            'processing_days_min' => $this->faker->numberBetween(3, 7),
            'processing_days_max' => $this->faker->numberBetween(14, 30),
            'allowed_stay_days' => $this->faker->randomElement([30, 60, 90, 180]),
            'validity_days' => $this->faker->randomElement([90, 180, 365]),
            'fee' => $this->faker->randomFloat(2, 25, 200),
            'service_fee' => $this->faker->randomFloat(2, 20, 80),
            'currency' => 'USD',
            'is_published' => true,
        ];
    }
}

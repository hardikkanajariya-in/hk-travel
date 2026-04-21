<?php

namespace App\Modules\Reviews\Database\Factories;

use App\Modules\Reviews\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        $rating = $this->faker->randomFloat(1, 3, 5);

        return [
            'reviewable_type' => 'App\\Modules\\Tours\\Models\\Tour',
            'reviewable_id' => (string) Str::ulid(),
            'user_id' => null,
            'author_name' => $this->faker->name(),
            'author_email' => $this->faker->safeEmail(),
            'title' => $this->faker->sentence(6),
            'body' => $this->faker->paragraphs(2, true),
            'rating' => $rating,
            'criteria' => [
                'value' => round($rating + $this->faker->randomFloat(1, -0.5, 0.5), 1),
                'service' => round($rating + $this->faker->randomFloat(1, -0.5, 0.5), 1),
                'quality' => round($rating + $this->faker->randomFloat(1, -0.5, 0.5), 1),
            ],
            'status' => Review::STATUS_PENDING,
            'is_verified' => $this->faker->boolean(30),
            'helpful_count' => 0,
            'reported_count' => 0,
            'locale' => 'en',
            'ip' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'approved_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => Review::STATUS_APPROVED,
            'approved_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => Review::STATUS_REJECTED]);
    }
}

<?php

namespace App\Modules\Blog\Database\Factories;

use App\Modules\Blog\Models\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogCategoryFactory extends Factory
{
    protected $model = BlogCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Travel Tips', 'Destinations', 'Food & Culture', 'Adventure', 'Luxury', 'Family', 'Solo Travel',
            'Budget', 'Photography', 'Wildlife', 'Beaches', 'Mountains', 'City Breaks', 'Wellness',
        ]);

        return [
            'parent_id' => null,
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'cover_image' => null,
            'position' => 0,
        ];
    }
}

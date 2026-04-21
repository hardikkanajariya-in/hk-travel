<?php

namespace App\Modules\Blog\Database\Factories;

use App\Modules\Blog\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(rand(4, 8));
        $body = collect(range(1, 6))->map(function ($i): string {
            $heading = $this->faker->sentence(4);
            $para1 = $this->faker->paragraphs(2, true);
            $para2 = $this->faker->paragraph();

            return "<h2>{$heading}</h2>\n<p>".str_replace("\n\n", '</p><p>', $para1)."</p>\n<p>{$para2}</p>";
        })->implode("\n\n");

        $words = max(1, str_word_count(strip_tags($body)));

        return [
            'author_id' => null,
            'title' => $title,
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'excerpt' => $this->faker->paragraph(),
            'body' => $body,
            'cover_image' => null,
            'gallery' => [],
            'status' => BlogPost::STATUS_PUBLISHED,
            'is_featured' => $this->faker->boolean(20),
            'allow_comments' => true,
            'show_toc' => true,
            'view_count' => $this->faker->numberBetween(0, 5000),
            'reading_minutes' => max(1, (int) ceil($words / 220)),
            'locale' => 'en',
            'published_at' => now()->subDays($this->faker->numberBetween(0, 90)),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => BlogPost::STATUS_DRAFT,
            'published_at' => null,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => BlogPost::STATUS_SCHEDULED,
            'published_at' => now()->addDays($this->faker->numberBetween(1, 14)),
        ]);
    }
}

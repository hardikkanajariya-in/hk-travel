<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Widget;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds the default starter content so a fresh install already feels like a
 * real travel-agency site: a homepage, a small set of marketing pages, the
 * primary + footer menus that link to them, and three footer widgets.
 *
 * Idempotent — running this seeder multiple times will only fill in missing
 * rows, never overwrite content the user has edited.
 */
class DefaultContentSeeder extends Seeder
{
    public function run(): void
    {
        $pages = $this->seedPages();
        $this->seedMenus($pages);
        $this->seedWidgets();
    }

    /**
     * @return array<string, Page>
     */
    protected function seedPages(): array
    {
        // Note: we deliberately do NOT seed a homepage Page row. With no Page
        // flagged as homepage, PageController::home() falls through to the
        // bundled rich `home.blade.php` template, which is the experience we
        // want a fresh install to ship with. Admins can later promote any
        // custom page to homepage from the Pages screen.
        $defaults = [
            'about' => [
                'title' => 'About us',
                'blocks' => [
                    ['type' => 'hero', 'data' => [
                        'eyebrow' => 'Our story',
                        'heading' => 'Travel that feels personal',
                        'subheading' => 'A small team of passionate travellers crafting unforgettable journeys for curious explorers.',
                        'image' => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=1600&q=80&auto=format&fit=crop',
                        'overlay' => 'dark',
                        'align' => 'center',
                    ]],
                    ['type' => 'rich-text', 'data' => [
                        'html' => '<h2>Why we started</h2><p>We believe travel is more than a destination — it is the people you meet, the meals you share, and the small wonders you stumble upon along the way. Every itinerary we design starts with a question: what would make <em>this</em> trip unforgettable for <em>you</em>?</p><h2>How we work</h2><p>Our local guides handpick every experience. Our concierge is one message away. And every booking is backed by a best-price guarantee.</p>',
                    ]],
                ],
            ],
            'tours' => [
                'title' => 'Tours',
                'blocks' => [
                    ['type' => 'hero', 'data' => [
                        'eyebrow' => 'Curated journeys',
                        'heading' => 'Find your next adventure',
                        'subheading' => 'Browse our collection of small-group tours, hand-built by people who have walked the path themselves.',
                        'image' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=1600&q=80&auto=format&fit=crop',
                        'overlay' => 'dark',
                    ]],
                    ['type' => 'rich-text', 'data' => [
                        'html' => '<p class="lead">Our full tour catalogue is launching soon. In the meantime, drop us a note and we will craft something just for you.</p>',
                    ]],
                ],
            ],
            'contact' => [
                'title' => 'Contact',
                'blocks' => [
                    ['type' => 'hero', 'data' => [
                        'eyebrow' => 'Say hello',
                        'heading' => 'We would love to hear from you',
                        'subheading' => 'Questions, custom itineraries, or just travel daydreams — we reply within one business day.',
                        'image' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=1600&q=80&auto=format&fit=crop',
                        'overlay' => 'dark',
                    ]],
                    ['type' => 'rich-text', 'data' => [
                        'html' => '<h3>Reach us</h3><p><strong>Email:</strong> hello@example.com<br><strong>Phone:</strong> +1 (555) 123-4567<br><strong>Hours:</strong> Mon–Sat, 9 a.m. – 7 p.m.</p>',
                    ]],
                ],
            ],
            'privacy' => [
                'title' => 'Privacy policy',
                'blocks' => [
                    ['type' => 'rich-text', 'data' => [
                        'html' => '<h1>Privacy policy</h1><p class="lead">We respect your privacy and are committed to protecting your personal information. This page is a starting template — replace it with your real policy before going live.</p><h2>What we collect</h2><p>We collect the information you provide when you contact us, sign up for our newsletter, or make a booking.</p><h2>How we use it</h2><p>To respond to your enquiry, deliver the service you asked for, and (with your permission) send you occasional updates.</p>',
                    ]],
                ],
            ],
            'terms' => [
                'title' => 'Terms of service',
                'blocks' => [
                    ['type' => 'rich-text', 'data' => [
                        'html' => '<h1>Terms of service</h1><p class="lead">By using this website you agree to the terms below. This page is a starting template — replace it with your real terms before going live.</p>',
                    ]],
                ],
            ],
        ];

        $created = [];

        foreach ($defaults as $slug => $cfg) {
            $page = Page::query()->firstOrNew(['slug' => $slug]);

            if ($page->exists) {
                $created[$slug] = $page;

                continue;
            }

            $page->fill([
                'title' => $cfg['title'],
                'layout' => $cfg['layout'] ?? 'default',
                'status' => 'published',
                'is_homepage' => $cfg['is_homepage'] ?? false,
                'allow_comments' => false,
                'published_at' => now(),
                'seo' => [
                    'title' => $cfg['title'],
                    'description' => Str::limit(strip_tags($cfg['blocks'][0]['data']['subheading'] ?? $cfg['title']), 160),
                ],
            ])->save();

            foreach (($cfg['blocks'] ?? []) as $i => $block) {
                $page->blocks()->create([
                    'type' => $block['type'],
                    'data' => $block['data'],
                    'position' => $i,
                ]);
            }

            $created[$slug] = $page;
        }

        return $created;
    }

    /**
     * @param  array<string, Page>  $pages
     */
    protected function seedMenus(array $pages): void
    {
        $primary = Menu::query()->firstOrCreate(
            ['location' => 'primary'],
            ['name' => 'Primary navigation', 'slug' => 'primary'],
        );

        if ($primary->wasRecentlyCreated || $primary->items()->count() === 0) {
            $items = [
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'Tours', 'slug' => 'tours'],
                ['label' => 'About', 'slug' => 'about'],
                ['label' => 'Contact', 'slug' => 'contact'],
            ];

            foreach ($items as $i => $item) {
                $page = isset($item['slug']) ? ($pages[$item['slug']] ?? null) : null;
                MenuItem::query()->create([
                    'menu_id' => $primary->id,
                    'label' => $item['label'],
                    'url' => $item['url'] ?? ($page ? '/'.$page->slug : '#'),
                    'link_type' => 'custom',
                    'position' => $i,
                ]);
            }
        }

        $footer = Menu::query()->firstOrCreate(
            ['location' => 'footer'],
            ['name' => 'Footer navigation', 'slug' => 'footer'],
        );

        if ($footer->wasRecentlyCreated || $footer->items()->count() === 0) {
            foreach ([
                ['label' => 'Privacy', 'slug' => 'privacy'],
                ['label' => 'Terms', 'slug' => 'terms'],
                ['label' => 'Contact', 'slug' => 'contact'],
            ] as $i => $item) {
                $page = $pages[$item['slug']] ?? null;
                MenuItem::query()->create([
                    'menu_id' => $footer->id,
                    'label' => $item['label'],
                    'url' => $page ? '/'.$page->slug : '#',
                    'link_type' => 'custom',
                    'position' => $i,
                ]);
            }
        }
    }

    protected function seedWidgets(): void
    {
        $defaults = [
            ['zone' => 'footer-1', 'type' => 'rich-text', 'position' => 0, 'data' => [
                'html' => '<h3 class="text-base font-semibold mb-3">About</h3><p class="text-sm text-zinc-600 dark:text-zinc-400">A small team of passionate travellers crafting unforgettable journeys. Hand-picked tours, transparent pricing, real human support.</p>',
            ]],
            ['zone' => 'footer-2', 'type' => 'menu', 'position' => 0, 'data' => [
                'location' => 'footer',
                'orientation' => 'vertical',
            ]],
            ['zone' => 'footer-3', 'type' => 'rich-text', 'position' => 0, 'data' => [
                'html' => '<h3 class="text-base font-semibold mb-3">Get in touch</h3><p class="text-sm text-zinc-600 dark:text-zinc-400">hello@example.com<br>+1 (555) 123-4567<br>Mon–Sat · 9 a.m. – 7 p.m.</p>',
            ]],
        ];

        foreach ($defaults as $widget) {
            Widget::query()->firstOrCreate(
                [
                    'zone' => $widget['zone'],
                    'type' => $widget['type'],
                    'position' => $widget['position'],
                ],
                [
                    'data' => $widget['data'],
                    'is_active' => true,
                ],
            );
        }
    }
}

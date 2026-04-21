<?php

namespace App\Modules\Destinations\Livewire\Public;

use App\Core\Modules\ModuleManager;
use App\Core\Seo\SeoManager;
use App\Modules\Activities\Models\Activity;
use App\Modules\Destinations\Models\Destination;
use App\Modules\Hotels\Models\Hotel;
use App\Modules\Tours\Models\Tour;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class DestinationShow extends Component
{
    public Destination $destination;

    public function mount(string $slug, SeoManager $seo): void
    {
        $this->destination = Destination::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $meta = $this->destination->toSeoMeta();
        $seo->title($meta['title'])
            ->description($meta['description'])
            ->image($meta['image'])
            ->canonical(route('destinations.show', $this->destination->slug));
    }

    public function render(ModuleManager $modules): View
    {
        $related = [
            'tours' => $modules->enabled('tours') && Schema::hasColumn('tours', 'destination_id')
                ? Tour::query()
                    ->where('destination_id', $this->destination->id)
                    ->where('is_published', true)
                    ->limit(6)->get()
                : collect(),
            'hotels' => $modules->enabled('hotels') && class_exists(Hotel::class) && Schema::hasColumn('hotels', 'destination_id')
                ? Hotel::query()
                    ->where('destination_id', $this->destination->id)
                    ->where('is_published', true)
                    ->limit(6)->get()
                : collect(),
            'activities' => $modules->enabled('activities') && class_exists(Activity::class) && Schema::hasColumn('activities', 'destination_id')
                ? Activity::query()
                    ->where('destination_id', $this->destination->id)
                    ->where('is_published', true)
                    ->limit(6)->get()
                : collect(),
        ];

        return view('destinations::public.show', [
            'destination' => $this->destination,
            'related' => $related,
        ]);
    }
}

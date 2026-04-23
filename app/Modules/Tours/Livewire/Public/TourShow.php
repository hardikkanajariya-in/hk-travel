<?php

namespace App\Modules\Tours\Livewire\Public;

use App\Core\Concerns\EnsuresCanonicalPublicUrl;
use App\Core\Routing\PublicUrlGenerator;
use App\Core\Seo\SeoManager;
use App\Modules\Tours\Models\Tour;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class TourShow extends Component
{
    use EnsuresCanonicalPublicUrl;

    public Tour $tour;

    public function mount(string $slug, SeoManager $seo, PublicUrlGenerator $urls): void
    {
        $this->tour = Tour::query()
            ->with('destination')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $meta = $this->tour->toSeoMeta();
        $seo->title($meta['title'])
            ->description($meta['description'])
            ->image($meta['image']);

        $this->ensureCanonicalPublicUrl('tour', $this->tour->slug, $seo, $urls);
    }

    public function render(): View
    {
        $related = Tour::query()
            ->where('is_published', true)
            ->whereKeyNot($this->tour->id)
            ->when($this->tour->destination_id, fn ($q) => $q->where('destination_id', $this->tour->destination_id))
            ->limit(3)
            ->get();

        return view('tours::public.show', [
            'tour' => $this->tour,
            'related' => $related,
        ]);
    }
}

<?php

namespace App\Modules\Hotels\Livewire\Public;

use App\Core\Concerns\EnsuresCanonicalPublicUrl;
use App\Core\Routing\PublicUrlGenerator;
use App\Core\Seo\SeoManager;
use App\Modules\Hotels\Models\Hotel;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class HotelShow extends Component
{
    use EnsuresCanonicalPublicUrl;

    public Hotel $hotel;

    public function mount(string $slug, SeoManager $seo, PublicUrlGenerator $urls): void
    {
        $this->hotel = Hotel::query()
            ->with(['destination', 'rooms' => fn ($q) => $q->where('is_available', true)])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $meta = $this->hotel->toSeoMeta();
        $seo->title($meta['title'])->description($meta['description'])->image($meta['image']);

        $this->ensureCanonicalPublicUrl('hotel', $this->hotel->slug, $seo, $urls);
    }

    public function render(): View
    {
        return view('hotels::public.show', ['hotel' => $this->hotel]);
    }
}

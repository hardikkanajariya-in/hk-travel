<?php

namespace App\Modules\Cars\Livewire\Public;

use App\Core\Concerns\EnsuresCanonicalPublicUrl;
use App\Core\Routing\PublicUrlGenerator;
use App\Core\Seo\SeoManager;
use App\Modules\Cars\Models\CarRental;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class CarShow extends Component
{
    use EnsuresCanonicalPublicUrl;

    public CarRental $car;

    public function mount(string $slug, SeoManager $seo, PublicUrlGenerator $urls): void
    {
        $this->car = CarRental::query()->where('slug', $slug)->where('is_published', true)->firstOrFail();
        $meta = $this->car->toSeoMeta();
        $seo->title($meta['title'])->description($meta['description']);

        $this->ensureCanonicalPublicUrl('car', $this->car->slug, $seo, $urls);
    }

    public function render(): View
    {
        return view('cars::public.show', ['car' => $this->car]);
    }
}

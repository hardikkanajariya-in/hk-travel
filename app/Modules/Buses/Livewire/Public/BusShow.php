<?php

namespace App\Modules\Buses\Livewire\Public;

use App\Core\Concerns\EnsuresCanonicalPublicUrl;
use App\Core\Routing\PublicUrlGenerator;
use App\Core\Seo\SeoManager;
use App\Modules\Buses\Models\BusRoute;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class BusShow extends Component
{
    use EnsuresCanonicalPublicUrl;

    public BusRoute $route;

    public function mount(string $slug, SeoManager $seo, PublicUrlGenerator $urls): void
    {
        $this->route = BusRoute::query()->where('slug', $slug)->where('is_published', true)->firstOrFail();
        $meta = $this->route->toSeoMeta();
        $seo->title($meta['title'])->description($meta['description']);

        $this->ensureCanonicalPublicUrl('bus', $this->route->slug, $seo, $urls);
    }

    public function render(): View
    {
        return view('buses::public.show', ['route' => $this->route]);
    }
}

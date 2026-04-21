<?php

namespace App\Modules\Buses\Livewire\Public;

use App\Core\Seo\SeoManager;
use App\Modules\Buses\Models\BusRoute;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class BusShow extends Component
{
    public BusRoute $route;

    public function mount(string $slug, SeoManager $seo): void
    {
        $this->route = BusRoute::query()->where('slug', $slug)->where('is_published', true)->firstOrFail();
        $meta = $this->route->toSeoMeta();
        $seo->title($meta['title'])->description($meta['description'])->canonical(route('buses.show', $this->route->slug));
    }

    public function render(): View
    {
        return view('buses::public.show', ['route' => $this->route]);
    }
}

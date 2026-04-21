<?php

namespace App\Modules\Taxi\Livewire\Public;

use App\Core\Seo\SeoManager;
use App\Modules\Taxi\Models\TaxiService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class TaxiShow extends Component
{
    public TaxiService $service;

    public function mount(string $slug, SeoManager $seo): void
    {
        $this->service = TaxiService::query()->where('slug', $slug)->where('is_published', true)->firstOrFail();
        $meta = $this->service->toSeoMeta();
        $seo->title($meta['title'])->description($meta['description'])->canonical(route('taxi.show', $this->service->slug));
    }

    public function render(): View
    {
        return view('taxi::public.show', ['service' => $this->service]);
    }
}

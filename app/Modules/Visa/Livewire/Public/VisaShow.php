<?php

namespace App\Modules\Visa\Livewire\Public;

use App\Core\Seo\SeoManager;
use App\Modules\Visa\Models\VisaService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class VisaShow extends Component
{
    public VisaService $service;

    public function mount(string $slug, SeoManager $seo): void
    {
        $this->service = VisaService::query()->where('slug', $slug)->where('is_published', true)->firstOrFail();
        $meta = $this->service->toSeoMeta();
        $seo->title($meta['title'])->description($meta['description'])->canonical(route('visa.show', $this->service->slug));
    }

    public function render(): View
    {
        return view('visa::public.show', ['service' => $this->service]);
    }
}

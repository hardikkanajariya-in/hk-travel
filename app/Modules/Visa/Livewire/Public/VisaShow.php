<?php

namespace App\Modules\Visa\Livewire\Public;

use App\Core\Concerns\EnsuresCanonicalPublicUrl;
use App\Core\Routing\PublicUrlGenerator;
use App\Core\Seo\SeoManager;
use App\Modules\Visa\Models\VisaService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class VisaShow extends Component
{
    use EnsuresCanonicalPublicUrl;

    public VisaService $service;

    public function mount(string $slug, SeoManager $seo, PublicUrlGenerator $urls): void
    {
        $this->service = VisaService::query()->where('slug', $slug)->where('is_published', true)->firstOrFail();
        $meta = $this->service->toSeoMeta();
        $seo->title($meta['title'])->description($meta['description']);

        $this->ensureCanonicalPublicUrl('visa', $this->service->slug, $seo, $urls);
    }

    public function render(): View
    {
        return view('visa::public.show', ['service' => $this->service]);
    }
}

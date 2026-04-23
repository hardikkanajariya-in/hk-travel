<?php

namespace App\Modules\Taxi\Livewire\Public;

use App\Core\Concerns\EnsuresCanonicalPublicUrl;
use App\Core\Routing\PublicUrlGenerator;
use App\Core\Seo\SeoManager;
use App\Modules\Taxi\Models\TaxiService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class TaxiShow extends Component
{
    use EnsuresCanonicalPublicUrl;

    public TaxiService $service;

    public function mount(string $slug, SeoManager $seo, PublicUrlGenerator $urls): void
    {
        $this->service = TaxiService::query()->where('slug', $slug)->where('is_published', true)->firstOrFail();
        $meta = $this->service->toSeoMeta();
        $seo->title($meta['title'])->description($meta['description']);

        $this->ensureCanonicalPublicUrl('taxi', $this->service->slug, $seo, $urls);
    }

    public function render(): View
    {
        return view('taxi::public.show', ['service' => $this->service]);
    }
}

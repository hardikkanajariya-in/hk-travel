<?php

namespace App\Modules\Cruises\Livewire\Public;

use App\Core\Seo\SeoManager;
use App\Modules\Cruises\Models\Cruise;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class CruiseShow extends Component
{
    public Cruise $cruise;

    public function mount(string $slug, SeoManager $seo): void
    {
        $this->cruise = Cruise::query()->where('slug', $slug)->where('is_published', true)->firstOrFail();
        $meta = $this->cruise->toSeoMeta();
        $seo->title($meta['title'])->description($meta['description'])->canonical(route('cruises.show', $this->cruise->slug));
    }

    public function render(): View
    {
        return view('cruises::public.show', ['cruise' => $this->cruise]);
    }
}

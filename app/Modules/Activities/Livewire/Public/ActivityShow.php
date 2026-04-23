<?php

namespace App\Modules\Activities\Livewire\Public;

use App\Core\Concerns\EnsuresCanonicalPublicUrl;
use App\Core\Routing\PublicUrlGenerator;
use App\Core\Seo\SeoManager;
use App\Modules\Activities\Models\Activity;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class ActivityShow extends Component
{
    use EnsuresCanonicalPublicUrl;

    public Activity $activity;

    public function mount(string $slug, SeoManager $seo, PublicUrlGenerator $urls): void
    {
        $this->activity = Activity::query()
            ->with('destination')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $meta = $this->activity->toSeoMeta();
        $seo->title($meta['title'])->description($meta['description'])->image($meta['image']);

        $this->ensureCanonicalPublicUrl('activity', $this->activity->slug, $seo, $urls);
    }

    public function render(): View
    {
        return view('activities::public.show', ['activity' => $this->activity]);
    }
}

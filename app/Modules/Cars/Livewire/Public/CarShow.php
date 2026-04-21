<?php

namespace App\Modules\Cars\Livewire\Public;

use App\Core\Seo\SeoManager;
use App\Modules\Cars\Models\CarRental;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class CarShow extends Component
{
    public CarRental $car;

    public function mount(string $slug, SeoManager $seo): void
    {
        $this->car = CarRental::query()->where('slug', $slug)->where('is_published', true)->firstOrFail();
        $meta = $this->car->toSeoMeta();
        $seo->title($meta['title'])->description($meta['description'])->canonical(route('cars.show', $this->car->slug));
    }

    public function render(): View
    {
        return view('cars::public.show', ['car' => $this->car]);
    }
}

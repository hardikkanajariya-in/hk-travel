<?php

namespace App\Modules\Tours\Livewire\Public;

use App\Modules\Tours\Models\Tour;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tour')]
#[Layout('components.layouts.installer')]
class TourShow extends Component
{
    public Tour $tour;

    public function mount(string $slug): void
    {
        $this->tour = Tour::query()->where('slug', $slug)->where('is_published', true)->firstOrFail();
    }

    public function render(): View
    {
        return view('tours::public.show', ['tour' => $this->tour]);
    }
}

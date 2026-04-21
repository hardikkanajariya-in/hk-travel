<?php

namespace App\Modules\Tours\Livewire\Public;

use App\Modules\Tours\Models\Tour;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Tours')]
#[Layout('components.layouts.installer')]
class TourIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public function render(): View
    {
        $tours = Tour::query()
            ->where('is_published', true)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('tours::public.index', compact('tours'));
    }
}

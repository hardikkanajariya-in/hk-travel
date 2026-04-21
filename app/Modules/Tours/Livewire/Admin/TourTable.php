<?php

namespace App\Modules\Tours\Livewire\Admin;

use App\Modules\Tours\Models\Tour;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Tours')]
#[Layout('components.layouts.admin')]
class TourTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function render(): View
    {
        $tours = Tour::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('tours::admin.table', compact('tours'));
    }
}

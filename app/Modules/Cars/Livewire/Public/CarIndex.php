<?php

namespace App\Modules\Cars\Livewire\Public;

use App\Modules\Cars\Models\CarRental;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Car rentals')]
#[Layout('components.layouts.public')]
class CarIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'class')]
    public string $class = '';

    #[Url(as: 'transmission')]
    public string $transmission = '';

    #[Url(as: 'sort')]
    public string $sort = 'price_asc';

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'class', 'transmission', 'sort'], true)) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        $cars = CarRental::query()
            ->where('is_published', true)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->class, fn ($q) => $q->where('vehicle_class', $this->class))
            ->when($this->transmission, fn ($q) => $q->where('transmission', $this->transmission))
            ->when($this->sort === 'price_asc', fn ($q) => $q->orderBy('daily_rate'))
            ->when($this->sort === 'price_desc', fn ($q) => $q->orderByDesc('daily_rate'))
            ->paginate(12);

        return view('cars::public.index', [
            'cars' => $cars,
            'classes' => ['economy', 'compact', 'sedan', 'suv', 'luxury', 'van'],
        ]);
    }
}

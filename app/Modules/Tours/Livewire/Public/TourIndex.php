<?php

namespace App\Modules\Tours\Livewire\Public;

use App\Modules\Destinations\Models\Destination;
use App\Modules\Tours\Models\Tour;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Tours')]
#[Layout('components.layouts.public')]
class TourIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'destination')]
    public string $destinationId = '';

    #[Url(as: 'difficulty')]
    public string $difficulty = '';

    #[Url(as: 'min')]
    public ?float $priceMin = null;

    #[Url(as: 'max')]
    public ?float $priceMax = null;

    #[Url(as: 'sort')]
    public string $sort = 'featured';

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'destinationId', 'difficulty', 'priceMin', 'priceMax', 'sort'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'destinationId', 'difficulty', 'priceMin', 'priceMax']);
        $this->sort = 'featured';
    }

    public function render(): View
    {
        $tours = Schema::hasTable('tours')
            ? Tour::query()
                ->with('destination')
                ->where('is_published', true)
                ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->when($this->destinationId, fn ($q) => $q->where('destination_id', $this->destinationId))
                ->when($this->difficulty, fn ($q) => $q->where('difficulty', $this->difficulty))
                ->when($this->priceMin !== null, fn ($q) => $q->where('price', '>=', $this->priceMin))
                ->when($this->priceMax !== null, fn ($q) => $q->where('price', '<=', $this->priceMax))
                ->when($this->sort === 'price_asc', fn ($q) => $q->orderBy('price'))
                ->when($this->sort === 'price_desc', fn ($q) => $q->orderByDesc('price'))
                ->when($this->sort === 'newest', fn ($q) => $q->orderByDesc('published_at'))
                ->when($this->sort === 'rating', fn ($q) => $q->orderByDesc('rating_avg'))
                ->when($this->sort === 'featured', fn ($q) => $q->orderByDesc('is_featured')->orderByDesc('published_at'))
                ->paginate(12)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12);

        return view('tours::public.index', [
            'tours' => $tours,
            'destinations' => Schema::hasTable('destinations')
                ? Destination::query()->where('is_published', true)->orderBy('name')->limit(200)->get(['id', 'name'])
                : collect(),
        ]);
    }
}

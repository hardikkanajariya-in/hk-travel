<?php

namespace App\Modules\Hotels\Livewire\Public;

use App\Modules\Destinations\Models\Destination;
use App\Modules\Hotels\Models\Hotel;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Hotels')]
#[Layout('components.layouts.public')]
class HotelIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'destination')]
    public string $destinationId = '';

    #[Url(as: 'stars')]
    public string $stars = '';

    #[Url(as: 'min')]
    public ?float $priceMin = null;

    #[Url(as: 'max')]
    public ?float $priceMax = null;

    #[Url(as: 'sort')]
    public string $sort = 'featured';

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'destinationId', 'stars', 'priceMin', 'priceMax', 'sort'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'destinationId', 'stars', 'priceMin', 'priceMax']);
        $this->sort = 'featured';
    }

    public function render(): View
    {
        $hotels = Hotel::query()
            ->with('destination')
            ->where('is_published', true)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->destinationId, fn ($q) => $q->where('destination_id', $this->destinationId))
            ->when($this->stars, fn ($q) => $q->where('star_rating', '>=', (int) $this->stars))
            ->when($this->priceMin !== null, fn ($q) => $q->where('price_from', '>=', $this->priceMin))
            ->when($this->priceMax !== null, fn ($q) => $q->where('price_from', '<=', $this->priceMax))
            ->when($this->sort === 'price_asc', fn ($q) => $q->orderBy('price_from'))
            ->when($this->sort === 'price_desc', fn ($q) => $q->orderByDesc('price_from'))
            ->when($this->sort === 'rating', fn ($q) => $q->orderByDesc('rating_avg'))
            ->when($this->sort === 'stars', fn ($q) => $q->orderByDesc('star_rating'))
            ->when($this->sort === 'featured', fn ($q) => $q->orderByDesc('is_featured')->orderByDesc('star_rating'))
            ->paginate(12);

        return view('hotels::public.index', [
            'hotels' => $hotels,
            'destinations' => Schema::hasTable('destinations')
                ? Destination::query()->where('is_published', true)->orderBy('name')->limit(200)->get(['id', 'name'])
                : collect(),
        ]);
    }
}

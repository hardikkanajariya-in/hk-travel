<?php

namespace App\Modules\Trains\Livewire\Public;

use App\Modules\Trains\Contracts\TrainProvider;
use App\Modules\Trains\DTO\TrainOfferData;
use App\Modules\Trains\DTO\TrainSearchCriteria;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Train search')]
#[Layout('components.layouts.public')]
class TrainSearch extends Component
{
    #[Url(as: 'from')]
    public string $origin = '';

    #[Url(as: 'to')]
    public string $destination = '';

    #[Url(as: 'depart')]
    public string $departDate = '';

    #[Url(as: 'return')]
    public string $returnDate = '';

    #[Url(as: 'adults')]
    public int $adults = 1;

    #[Url(as: 'children')]
    public int $children = 0;

    #[Url(as: 'class')]
    public string $class = 'standard';

    #[Url(as: 'sort')]
    public string $sort = 'price_asc';

    public bool $hasSearched = false;

    /** @var Collection<int, TrainOfferData> */
    public Collection $results;

    public function mount(): void
    {
        $this->results = collect();
        $this->departDate = $this->departDate ?: now()->addDays(7)->toDateString();
        if ($this->origin && $this->destination && $this->departDate) {
            $this->search();
        }
    }

    public function search(): void
    {
        $this->validate([
            'origin' => 'required|string|min:2|max:8',
            'destination' => 'required|string|min:2|max:8|different:origin',
            'departDate' => 'required|date|after_or_equal:today',
            'returnDate' => 'nullable|date|after_or_equal:departDate',
            'adults' => 'required|integer|min:1|max:9',
        ]);

        /** @var TrainProvider $provider */
        $provider = app(TrainProvider::class);
        $criteria = new TrainSearchCriteria(
            origin: strtoupper($this->origin),
            destination: strtoupper($this->destination),
            departDate: $this->departDate,
            returnDate: $this->returnDate ?: null,
            adults: $this->adults,
            children: $this->children,
            class: $this->class,
        );

        $this->results = $provider->search($criteria);
        $this->hasSearched = true;
        $this->applySort();
    }

    public function updatedSort(): void
    {
        if ($this->hasSearched) {
            $this->applySort();
        }
    }

    protected function applySort(): void
    {
        $this->results = match ($this->sort) {
            'price_desc' => $this->results->sortByDesc('price')->values(),
            'duration' => $this->results->sortBy('durationMinutes')->values(),
            'depart' => $this->results->sortBy('departTime')->values(),
            default => $this->results->sortBy('price')->values(),
        };
    }

    public function render(): View
    {
        return view('trains::public.index');
    }
}

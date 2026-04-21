<?php

namespace App\Modules\Flights\Livewire\Public;

use App\Modules\Flights\Contracts\FlightProvider;
use App\Modules\Flights\DTO\FlightOfferData;
use App\Modules\Flights\DTO\FlightSearchCriteria;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Flight search')]
#[Layout('components.layouts.public')]
class FlightSearch extends Component
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

    #[Url(as: 'cabin')]
    public string $cabin = 'economy';

    #[Url(as: 'sort')]
    public string $sort = 'price_asc';

    public bool $hasSearched = false;

    /** @var Collection<int, FlightOfferData> */
    public Collection $results;

    public function mount(): void
    {
        $this->results = collect();
        $this->departDate = $this->departDate ?: now()->addDays(14)->toDateString();
        if ($this->origin && $this->destination && $this->departDate) {
            $this->search();
        }
    }

    public function search(): void
    {
        $this->validate([
            'origin' => 'required|string|size:3',
            'destination' => 'required|string|size:3|different:origin',
            'departDate' => 'required|date|after_or_equal:today',
            'returnDate' => 'nullable|date|after_or_equal:departDate',
            'adults' => 'required|integer|min:1|max:9',
        ]);

        /** @var FlightProvider $provider */
        $provider = app(FlightProvider::class);
        $criteria = new FlightSearchCriteria(
            origin: strtoupper($this->origin),
            destination: strtoupper($this->destination),
            departDate: $this->departDate,
            returnDate: $this->returnDate ?: null,
            adults: $this->adults,
            children: $this->children,
            cabin: $this->cabin,
        );

        $this->results = $provider->search($criteria);
        $this->hasSearched = true;

        $this->results = match ($this->sort) {
            'price_desc' => $this->results->sortByDesc('price')->values(),
            'duration' => $this->results->sortBy('durationMinutes')->values(),
            'depart' => $this->results->sortBy('departTime')->values(),
            default => $this->results->sortBy('price')->values(),
        };
    }

    public function updatedSort(): void
    {
        if ($this->hasSearched) {
            $this->search();
        }
    }

    public function render(): View
    {
        return view('flights::public.index');
    }
}

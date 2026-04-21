<?php

namespace App\Modules\Flights\Livewire\Admin;

use App\Modules\Flights\Models\FlightOffer;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Flight offers')]
#[Layout('components.layouts.admin')]
class FlightOfferTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function updating(string $name): void
    {
        if ($name === 'search') {
            $this->resetPage();
        }
    }

    public function togglePublish(string $id): void
    {
        $this->authorize('flights.update');
        $row = FlightOffer::query()->findOrFail($id);
        $row->is_published = ! $row->is_published;
        $row->save();
    }

    public function delete(string $id): void
    {
        $this->authorize('flights.delete');
        FlightOffer::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $offers = FlightOffer::query()
            ->when($this->search, fn ($q) => $q->where(fn ($qq) => $qq->where('origin', 'like', "%{$this->search}%")
                ->orWhere('destination', 'like', "%{$this->search}%")->orWhere('airline', 'like', "%{$this->search}%")))
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('flights::admin.table', compact('offers'));
    }
}

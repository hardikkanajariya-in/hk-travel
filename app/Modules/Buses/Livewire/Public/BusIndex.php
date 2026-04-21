<?php

namespace App\Modules\Buses\Livewire\Public;

use App\Modules\Buses\Models\BusRoute;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Bus routes')]
#[Layout('components.layouts.public')]
class BusIndex extends Component
{
    use WithPagination;

    #[Url(as: 'from')]
    public string $origin = '';

    #[Url(as: 'to')]
    public string $destination = '';

    #[Url(as: 'type')]
    public string $type = '';

    public function updating(string $name): void
    {
        if (in_array($name, ['origin', 'destination', 'type'], true)) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        $routes = Schema::hasTable('bus_routes')
            ? BusRoute::query()
                ->where('is_published', true)
                ->when($this->origin, fn ($q) => $q->where('origin', 'like', "%{$this->origin}%"))
                ->when($this->destination, fn ($q) => $q->where('destination', 'like', "%{$this->destination}%"))
                ->when($this->type, fn ($q) => $q->where('bus_type', $this->type))
                ->orderBy('departure_time')
                ->paginate(15)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);

        return view('buses::public.index', compact('routes'));
    }
}

<?php

namespace App\Modules\Taxi\Livewire\Public;

use App\Modules\Taxi\Models\TaxiService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Taxi & transfers')]
#[Layout('components.layouts.public')]
class TaxiIndex extends Component
{
    use WithPagination;

    #[Url(as: 'type')]
    public string $type = '';

    #[Url(as: 'vehicle')]
    public string $vehicle = '';

    public function updating(string $name): void
    {
        if (in_array($name, ['type', 'vehicle'], true)) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        $services = Schema::hasTable('taxi_services')
            ? TaxiService::query()
                ->where('is_published', true)
                ->when($this->type, fn ($q) => $q->where('service_type', $this->type))
                ->when($this->vehicle, fn ($q) => $q->where('vehicle_type', $this->vehicle))
                ->orderBy('flat_rate')
                ->paginate(12)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12);

        return view('taxi::public.index', compact('services'));
    }
}
